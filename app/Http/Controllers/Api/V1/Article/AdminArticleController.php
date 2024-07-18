<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Article\CreateArticleRequest;
use App\Http\Requests\Api\V1\Article\UpdateArticleRequest;
use App\Http\Resources\Api\V1\Article\ArticleResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\Article;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminArticleController extends Controller
{
    public function index()
    {
        $article = Article::exceptBody()->paginate(25);

        return ArticleResource::collection($article);
    }

    public function show(Article $article)
    {
        $article = $article->load([
            'author', 'category'
        ]);

        return new ArticleResource($article);
    }

    public function store(CreateArticleRequest $request)
    {
        try {
            DB::beginTransaction();
            $request        = $request->validated();
            $article        = new Article();
            $imageService   = new ImageService($article);
    
            $article->title         = $request['title'];
            $article->author_id     = $request['author_id'];
            $article->category_id   = $request['category_id'];
            $article->body          = $request['body'];
            $article->meta          = $request['meta'];
            $article->image_url     = $imageService->upload($request->image)->path;
            $article->views         = 0;

            $article->save();

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
            $article->body          = $request['body'];
            $article->meta          = $request['meta'];
    
            if ($request->has('image')) {
                $imageService       = new ImageService($article);
                $article->image_url = $imageService->update($request->image)->path; 
            }

            $article->save();
            
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
}
