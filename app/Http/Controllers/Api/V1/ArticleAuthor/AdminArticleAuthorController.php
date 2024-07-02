<?php

namespace App\Http\Controllers\Api\V1\ArticleAuthor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ArticleAuthor\CreateAuthorRequest;
use App\Http\Requests\Api\V1\ArticleAuthor\UpdateAuthorRequest;
use App\Http\Resources\Api\V1\ArticleAuthor\ArticleAuthorResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\ArticleAuthor;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminArticleAuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = ArticleAuthor::paginate(25);

        return ArticleAuthorResource::collection($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAuthorRequest $request)
    {
        try {
            DB::beginTransaction();
            $author             = new ArticleAuthor();
            $author->first_name = $request->first_name;
            $author->last_name  = $request->last_name;

            $imageService = new ImageService($author);
            $result       = $imageService->upload($request->image);

            $author->image_url = $result->path;

            $author->save();

            DB::commit();

            return new SuccessResource(JsonResponse::HTTP_CREATED);

        } catch (\Throwable $th) {

            DB::rollBack();
            $imageService->rollback();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ArticleAuthor $author)
    {
        return new ArticleAuthorResource($author);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, ArticleAuthor $author)
    {
        try {
            DB::beginTransaction();

            $author->first_name = $request->first_name;
            $author->last_name  = $request->last_name;

            if ($request->has('image')) {

                $imageService = new ImageService($author);
                $result       = $imageService->update($request->image);

                $author->image_url = $result->path;
            }
            $author->save();
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArticleAuthor $author)
    {
        try {
            DB::beginTransaction();

            $imageService = new ImageService($author);
            $imageService->delete();
            $author->delete();

            DB::commit();

        } catch (\Throwable $th) {
            
            DB::rollBack();
            $imageService->rollback();
            throw $th;
        }
    }
}
