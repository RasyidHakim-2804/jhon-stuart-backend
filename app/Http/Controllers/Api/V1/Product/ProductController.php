<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Http\Resources\Api\V1\Product\ProductFullResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['images'])->paginate(25);

        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with(['variants', 'categories', 'images'])->firstOrFail();
        
        return new ProductFullResource($product);
    }
}
