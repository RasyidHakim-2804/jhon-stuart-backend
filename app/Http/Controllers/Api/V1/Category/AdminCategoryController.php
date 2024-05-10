<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Category\CreateCategoryRequest;
use App\Http\Requests\Api\V1\Category\UpdateCategoryRequest;
use App\Http\Resources\Api\V1\Category\CategoryResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\Category;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->paginate(25);
    
        return CategoryResource::collection($categories);
    }

    public function store(CreateCategoryRequest $request)
    {
        $category = new Category();
        $category->name = $request->name;

        $category->save();

        return new SuccessResource(201);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, $category)
    {
        $category       = Category::findOrFail($category);
        $category->name = $request->name;

        $category->save();

        return new SuccessResource();
    }

    public function destroy(Category $category)
    {
        $category->products()->detach();
        $category->delete();

        return new SuccessResource();
    }
}
