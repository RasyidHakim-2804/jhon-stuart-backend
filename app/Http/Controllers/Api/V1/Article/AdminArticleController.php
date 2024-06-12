<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Article\CreateArticleRequest;
use App\Http\Requests\Api\V1\Article\UpdateArticleRequest;
use App\Http\Resources\Api\V1\Article\AdminArticleResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\Article;
use App\Models\Translation\ArticleTranslation;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminArticleController extends Controller
{
    public function index()
    {
        $article = Article::paginate(25);

        return AdminArticleResource::collection($article);
    }

    public function show($slug)
    {
        $article = Article::with([
            'translations', 'author', 'category'
            ])->where('slug', $slug)->first();

        return new AdminArticleResource($article);
    }

    public function store(CreateArticleRequest $request)
    {
        try {
            DB::beginTransaction();
            $requestArr     = $request->validated();
            $article        = new Article();
            $imageService   = new ImageService($article);
            $result         = $imageService->upload($request->image);
    
            $article->name          = $requestArr['title']['en'];
            $article->author_id     = $requestArr['author_id'];
            $article->category_id   = $requestArr['category_id'];
            $article->image_url     = $result->path;
            $article->views         = 1;
            $article->save();
    
            foreach ( $request['title'] as $lang => $value) {
                $this->setTranslation($requestArr, $lang, $article->id);
            }

            DB::commit();

            return new SuccessResource(JsonResponse::HTTP_CREATED);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        try {
            DB::beginTransaction();
            $requestArr = $request->validated();
    
            $article->name          = $requestArr['title']['en'];
            $article->author_id     = $requestArr['author_id'];
            $article->category_id   = $requestArr['category_id'];
    
            foreach ( $request['title'] as $lang => $value) {
                $this->setTranslation($requestArr, $lang, $article->id);
            }
    
            if ($request->has('image')) {
                $imageService       = new ImageService($article);
                $article->image_url = $imageService->update($request->image)->path; 
            }

            $article->save();
            
            DB::commit();

            return new SuccessResource();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }    
    }

    public function destroy(Article $article)
    {
        try {
            DB::beginTransaction();

            (new ImageService($article))->delete();
            $article->translation()->delete();
            $article->delete();
            
            DB::commit();
            
            return new SuccessResource();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
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
        
        $translation->article_id    = $article_id;
        $translation->lang          = $lang;
        $translation->title         = $request['title'][$lang];
        $translation->body          = $request['body'][$lang];
        $translation->save();
    }
}
