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

    public function show(Article $article)
    {
        $article = $article->load([
            'translations', 'author', 'category'
            ]);

        return new AdminArticleResource($article);
    }

    public function store(CreateArticleRequest $request)
    {
        try {
            DB::beginTransaction();
            $request        = $request->validated();
            $article        = new Article();
            $imageService   = new ImageService($article);
            $result         = $imageService->upload($request->image);
    
            $article->title         = $request['title'];
            $article->author_id     = $request['author_id'];
            $article->category_id   = $request['category_id'];
            $article->image_url     = $result->path;
            $article->views         = 0;
            $article->save();
    
            $this->setTranslation($request, $article->id);

            DB::commit();

            return new SuccessResource(JsonResponse::HTTP_CREATED);

        } catch (\Throwable $th) {
            
            DB::rollBack();
            $imageService->rollback();
            
            throw $th;
        }
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        try {
            DB::beginTransaction();
            $requestArr             = $request->validated();
            $article->title         = $requestArr['title'];
            $article->author_id     = $requestArr['author_id'];
            $article->category_id   = $requestArr['category_id'];
    
            if ($request->has('image')) {
                $imageService       = new ImageService($article);
                $article->image_url = $imageService->update($request->image)->path; 
            }

            $article->save();

            $this->setTranslation($requestArr, $article->id);
            
            DB::commit();

            return new SuccessResource();

        } catch (\Throwable $th) {
            DB::rollBack();

            if (isset($imageService)) {
                $imageService->rollback();
            }

            throw $th;
        }    
    }

    public function destroy(Article $article)
    {
        try {
            DB::beginTransaction();

            $imageService = new ImageService($article);
            $imageService->delete();
            $article->translation()->delete();
            $article->delete();
            
            DB::commit();
            
            return new SuccessResource();

        } catch (\Throwable $th) {
            DB::rollBack();

            if (isset($imageService)) {
                $imageService->rollback();
            }
            
            throw $th;
        }
    }

    //////////////////////
    // private functions//
    //////////////////////

    private function setTranslation( array $request, $article_id)
    {
        foreach ( ['id', 'en'] as $lang) {
            $translation                = ArticleTranslation::where('lang', $lang)
                                            ->where('article_id', $article_id)
                                            ->firstOrNew();
                                            
            $translation->body          = $request[$lang . '_body'];
            $translation->product_id    = $article_id;
            $translation->lang          = $lang;
            $translation->save();
        }
    }
}
