<?php

namespace App\Http\Controllers\Api\V1\ArticleCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ArticleCategory\ArticleCategoryRequest;
use App\Http\Resources\Api\V1\ArticleCategory\ArticleCategoryResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\ArticleCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminArticleCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ArticleCategory::paginate(25);

        return ArticleCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleCategoryRequest $request)
    {
        $category       = new ArticleCategory();
        $category->name = $request->name;
        $category->save();

        return new SuccessResource(JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(ArticleCategory $articleCategory)
    {
        return new ArticleCategoryResource($articleCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArticleCategoryRequest $request, ArticleCategory $category)
    {
        $category->name = $request->name;
        $category->save();

        return new SuccessResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArticleCategory $category)
    {
        $category->delete();

        return new SuccessResource();
    }
}
