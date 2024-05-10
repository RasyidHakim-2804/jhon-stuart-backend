<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\Api\V1\Category\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->paginate(25);
        return CategoryResource::collection($categories);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->with('products')->firstOrFail();

        return new CategoryResource($category);
    }
}
