<?php

namespace App\Http\Controllers\Api\V1\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Event\EventRequest;
use App\Http\Resources\Event\AdminEventResource;
use App\Http\Resources\Event\AdminListEventResource;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\EventTranslation;
use App\Models\EventVideo;
use App\Models\Language;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminEventController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        return AdminListEventResource::collection(
            QueryBuilder::for(Event::class)
            ->allowedFilters([
                AllowedFilter::scope('search')
            ])
            ->latest()->with([
                'metaTranslations' => fn($query) => $query->where('language', '=', 'en'),
                'author'
            ])->paginate(25));
    }

    public function show(Event $article)
    {
        // $this->permissionService->check('view');

        $article = new AdminEventResource($article->load([
            'bodyTranslations',
            'metaTranslations',
            'titleTranslations',
            'author',
        ]));

        return $this->successGet($article);
    }

    public function store(EventRequest $request)
    {
        DB::beginTransaction();
        try {
            // $this->permissionService->check('full');

            $article                = new Event();
            $article->title         = $request->title['en'];
            $article->category_id   = $request->category_id;
            $article->video         = $request->video;
            $article->total_view    = 0;
            $article->admin_id      = auth()->user()->id;

            $article->save();

            $request = $request->validated();
            foreach (EventTranslation::ATTRIBUTE as $field) {
                foreach (Language::code() as $lang) {
                    $this->saveEventTranslation($article->id, $field, $lang, $request[$field][$lang]);
                }
            }

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function update(EventRequest $request, Event $article)
    {
        DB::beginTransaction();
        try {
            // $this->permissionService->check('full');

            $article->title         = $request->title['en'];
            $article->category_id   = $request->category_id;
            $article->video         = $request->video;
            $article->admin_id      = auth()->user()->id;

            $article->save();

            $request = $request->validated();
            foreach (EventTranslation::ATTRIBUTE as $field) {
                foreach (Language::code() as $lang) {
                    $this->saveEventTranslation($article->id, $field, $lang, $request[$field][$lang]);
                }
            }

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function destroy(Event $article)
    {
        // $this->permissionService->check('full');

        $article->delete();

        if ($article->image) $this->deleteImage($article);

        return $this->successStore();
    }

    // === PROTECTED METHOD ===

    protected function saveThumbnail($articleImage, $uploadedImage)
    {
        $image = (new ImageService($articleImage->imagePath(), $uploadedImage));

        $image->save();

        $articleImage->image = $image->getImageName();

        return $articleImage;
    }

    protected function saveImages($article, $bodies)
    {
        $uploadedImages = $this->getUsedImagesName($bodies);

        $this->deleteOldImages($article, $uploadedImages);

        EventImage::whereIn('image', $uploadedImages)->update(['article_id' => $article->id]);

        $this->deleteUnusedImage();
    }

    protected function getUsedImagesName($bodies)
    {
        $imagesName = [];

        foreach ($bodies as $lang => $body) {
            $imageUrls = explode('img', $body);
            $regex     = "/[\w\-]+\.(jpg|png|gif|jpeg)/";

            if (isset($imageUrls)) {
                foreach ($imageUrls as $key => $imageUrl) {
                    if(preg_match($regex, $imageUrl, $fileName)) {
                        $imagesName[] = $fileName[0];
                    }
                }
            }
        }

        return $imagesName;
    }

    protected function deleteOldImages($article, $uploadedImages)
    {
        $oldImages = EventImage::where('article_id', $article->id)->whereNotIn('image', $uploadedImages)->get();

        if (isset($oldImages)) {
            foreach ($oldImages as $key => $oldImage) {
                $this->deleteImage($oldImage);
            }
        }
    }

    protected function deleteUnusedImage()
    {
        $unusedImages = EventImage::where('article_id', null)->get();

        if (isset($unusedImages)) {
            foreach ($unusedImages as $key => $unusedImage) {
                $this->deleteImage($unusedImage);
            }
        }
    }

    protected function deleteImage($dataImage)
    {
        $image = (new ImageService($dataImage->imagePath()));

        $image->delete($dataImage->image);
    }

    protected function saveEventTranslation($articleId, $field, $lang, $translation)
    {
        $newTranslation = EventTranslation::where('article_id', $articleId)->where('field', $field)->where('language', $lang)->first();

        if (!$newTranslation) $newTranslation = new EventTranslation();

        $newTranslation->article_id    = $articleId;
        $newTranslation->field         = $field;
        $newTranslation->language      = $lang;
        $newTranslation->translation   = $translation;

        $newTranslation->save();
    }
}
