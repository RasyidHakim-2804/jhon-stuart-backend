<?php

namespace App\Http\Controllers\Api\V1\Article;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Article\CreateArticleRequest;
use App\Http\Requests\Api\V1\Article\UpdateArticleRequest;
use App\Http\Resources\Api\V1\Article\AdminArticleResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\Article;
use App\Models\Image\ArticleImage;
use App\Models\Translation\ArticleTranslation;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AdminArticleController extends Controller
{
    public function index()
    {
        $article = Article::with([
            'translation', 'image'
        ])->paginate(25);

        return AdminArticleResource::collection($article);
    }

    public function show($slug)
    {
        $article = Article::with([
            'translation', 'image'
        ])->where('slug', $slug)->first();

        return new AdminArticleResource($article);
    }

    public function store(CreateArticleRequest $request)
    {
        DB::transaction(function() use ($request) {
            $requestArr = $request->validated();
            $article    = new Article();
    
            $article->slug = Str::slug( $requestArr['title']['en'] );
            $article->save();
    
            foreach ( $request['title'] as $lang => $value) {
                $this->setTranslation($requestArr, $lang, $article->id);
            }
    
            $this->setImage($request->image, $article->id);    
        });
        
        return new SuccessResource(JsonResponse::HTTP_CREATED);
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        DB::transaction(function() use ($request, $article) {
            $requestArr = $request->validated();
    
            $article->slug = Str::slug( $requestArr['title']['en'] );
            $article->save();
    
            foreach ( $request['title'] as $lang => $value) {
                $this->setTranslation($requestArr, $lang, $article->id);
            }
    
            if ($request->has('image')) {
                $this->setImage($request->image, $article->id);    
            }
        });
        
        return new SuccessResource();
    }

    public function destroy(Article $article)
    {
        DB::transaction(function() use ($article) {
            $article->translation()->delete();
            $article->image()->delete();
            $article->delete();
        });
        
        return new SuccessResource();
    }

    //////////////////////
    // private functions//
    //////////////////////

    private function setTranslation(array $request, $lang, $article_id)
    {
        $translation = ArticleTranslation::query()
                        ->where('article_id', $article_id)
                        ->where('lang', $lang)
                        ->firstOrNew();
        
        $translation->article_id = $article_id;
        $translation->lang       = $lang;
        $translation->title      = $request['title'][$lang];
        $translation->body       = $request['body'][$lang];
        $translation->save();
    }

    private function setImage(UploadedFile $image, $article_id)
    {
        /** @var ArticleImage */
        $articleImage = ArticleImage::query()
                    ->where('article_id', $article_id)
                    ->firstOrNew();

        $imageService = new ImageService($articleImage);

        if ( $articleImage->id ) { // update
            $result = $imageService->update( $image );
        } else { // create 
            $result = $imageService->upload( $image );
        }

        $articleImage->article_id = $article_id;
        $articleImage->name       = $result->name;
        $articleImage->path       = $result->path;
        $articleImage->save();
        
    }
}
