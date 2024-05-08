<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\Product\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['variants', 'categories'])->paginate(25);

        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $product = Product::findBySlug($slug);

        return new ProductResource($product);
    }
}
