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
        // $this->permissionService = (new AdminPermissionService('event'));
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

    public function show(Event $event)
    {
        // $this->permissionService->check('view');

        $event = new AdminEventResource($event->load([
            'bodyTranslations',
            'metaTranslations',
            'titleTranslations',
            'author',
        ]));

        return $this->successGet($event);
    }

    public function store(EventRequest $request)
    {
        DB::beginTransaction();
        try {
            // $this->permissionService->check('full');

            $event                = new Event();
            $event->title         = $request->title['en'];
            $event->category_id   = $request->category_id;
            $event->video         = $request->video;
            $event->total_view    = 0;
            $event->admin_id      = auth()->user()->id;

            $event->save();

            $request = $request->validated();
            foreach (EventTranslation::ATTRIBUTE as $field) {
                foreach (Language::code() as $lang) {
                    $this->saveEventTranslation($event->id, $field, $lang, $request[$field][$lang]);
                }
            }

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function update(EventRequest $request, Event $event)
    {
        DB::beginTransaction();
        try {
            // $this->permissionService->check('full');

            $event->title         = $request->title['en'];
            $event->category_id   = $request->category_id;
            $event->video         = $request->video;
            $event->admin_id      = auth()->user()->id;

            $event->save();

            $request = $request->validated();
            foreach (EventTranslation::ATTRIBUTE as $field) {
                foreach (Language::code() as $lang) {
                    $this->saveEventTranslation($event->id, $field, $lang, $request[$field][$lang]);
                }
            }

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function destroy(Event $event)
    {
        // $this->permissionService->check('full');

        $event->delete();

        if ($event->image) $this->deleteImage($event);

        return $this->successStore();
    }

    // === PROTECTED METHOD ===

    protected function saveThumbnail($eventImage, $uploadedImage)
    {
        $image = (new ImageService($eventImage->imagePath(), $uploadedImage));

        $image->save();

        $eventImage->image = $image->getImageName();

        return $eventImage;
    }

    protected function saveImages($event, $bodies)
    {
        $uploadedImages = $this->getUsedImagesName($bodies);

        $this->deleteOldImages($event, $uploadedImages);

        EventImage::whereIn('image', $uploadedImages)->update(['event_id' => $event->id]);

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

    protected function deleteOldImages($event, $uploadedImages)
    {
        $oldImages = EventImage::where('event_id', $event->id)->whereNotIn('image', $uploadedImages)->get();

        if (isset($oldImages)) {
            foreach ($oldImages as $key => $oldImage) {
                $this->deleteImage($oldImage);
            }
        }
    }

    protected function deleteUnusedImage()
    {
        $unusedImages = EventImage::where('event_id', null)->get();

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

    protected function saveEventTranslation($eventId, $field, $lang, $translation)
    {
        $newTranslation = EventTranslation::where('event_id', $eventId)->where('field', $field)->where('language', $lang)->first();

        if (!$newTranslation) $newTranslation = new EventTranslation();

        $newTranslation->event_id    = $eventId;
        $newTranslation->field         = $field;
        $newTranslation->language      = $lang;
        $newTranslation->translation   = $translation;

        $newTranslation->save();
    }
}
