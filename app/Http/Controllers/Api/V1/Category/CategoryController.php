<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\Api\V1\Category\CategoryResource;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\Product;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->paginate(25);
        return CategoryResource::collection($categories);
    }

    public function show($slug)
    {
        $lang = request()->getLanguages()[0];

        $products = Product::with([
            'variants',
            'categories',
            'images' => function($query){
                $query->where('is_saved', true)->orderBy('position', 'asc');
            },
            'translations' => function($query) use ($lang) {
                $query->where('lang', $lang);
            }
        ])->whereHas('categories', function($query) use ($slug) {
            $query->where('slug', $slug);
        })->paginate(25);

        return ProductResource::collection($products);
    }
}
