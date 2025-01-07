<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Article\ArticleRequest;
use App\Http\Resources\Article\AdminArticleResource;
use App\Http\Resources\Article\AdminListArticleResource;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\ArticleTranslation;
use App\Models\ArticleVideo;
use App\Models\Language;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminArticleController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        return AdminListArticleResource::collection(
            QueryBuilder::for(Article::class)
            ->allowedFilters([
                'type',
                AllowedFilter::scope('search')
            ])
            ->latest()->with([
                'metaTranslations' => fn($query) => $query->where('language', '=', 'en'),
                'author'
            ])->paginate(25));
    }

    public function show(Article $article)
    {
        // $this->permissionService->check('view');

        $article = new AdminArticleResource($article->load([
            'bodyTranslations',
            'metaTranslations',
            'titleTranslations',
            'author',
            'video',
        ]));

        return $this->successGet($article);
    }

    public function store(ArticleRequest $request)
    {
        DB::beginTransaction();
        try {
            // $this->permissionService->check('full');

            $article                = new Article();
            $article->title         = $request->title['en'];
            $article->category_id   = $request->category_id;
            $article->type          = $request->type;
            $article->total_view    = 0;
            $article->admin_id      = auth()->user()->id;

            $article = $this->saveThumbnail($article, $request->thumbnail);

            $article->save();

            if ($article->type == 'event') {
                $this->saveVideo($article, $request->video);
            }

            $request = $request->validated();
            foreach (ArticleTranslation::BLOG_ATTRIBUTE as $field) {
                foreach (Language::code() as $lang) {
                    $this->saveArticleTranslation($article->id, $field, $lang, $request[$field][$lang]);
                }
            }

            $this->saveImages($article, $request['body']);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function update(ArticleRequest $request, Article $article)
    {
        DB::beginTransaction();
        try {
            // $this->permissionService->check('full');

            if ($request->thumbnail) {
                $this->deleteImage($article);

                $article = $this->saveThumbnail($article, $request->thumbnail);
            }

            $article->title         = $request->title['en'];
            $article->category_id   = $request->category_id;
            $article->type          = $request->type;
            $article->admin_id      = auth()->user()->id;

            $article->save();

            if ($article->type == 'event') {
                $this->saveVideo($article, $request->url);
            }

            $request = $request->validated();
            foreach (ArticleTranslation::BLOG_ATTRIBUTE as $field) {
                foreach (Language::code() as $lang) {
                    $this->saveArticleTranslation($article->id, $field, $lang, $request[$field][$lang]);
                }
            }

            $this->saveImages($article, $request['body']);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function destroy(Article $article)
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

        ArticleImage::whereIn('image', $uploadedImages)->update(['article_id' => $article->id]);

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
        $oldImages = ArticleImage::where('article_id', $article->id)->whereNotIn('image', $uploadedImages)->get();

        if (isset($oldImages)) {
            foreach ($oldImages as $key => $oldImage) {
                $this->deleteImage($oldImage);
            }
        }
    }

    protected function deleteUnusedImage()
    {
        $unusedImages = ArticleImage::where('article_id', null)->get();

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

    protected function saveArticleTranslation($articleId, $field, $lang, $translation)
    {
        $newTranslation = ArticleTranslation::where('article_id', $articleId)->where('field', $field)->where('language', $lang)->first();

        if (!$newTranslation) $newTranslation = new ArticleTranslation();

        $newTranslation->article_id    = $articleId;
        $newTranslation->field         = $field;
        $newTranslation->language      = $lang;
        $newTranslation->translation   = $translation;

        $newTranslation->save();
    }

    protected function saveVideo(Article $article, $url)
    {
        $articleVideo             = ArticleVideo::where('article_id', '=', $article->id)->firstOrNew();
        $articleVideo->video      = $url;
        $articleVideo->article_id = $article->id;
        $articleVideo->save();
    }
}
