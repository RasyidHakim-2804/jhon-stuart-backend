<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Service\Services\SearchServices\ProductSearchService;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return (new ProductSearchService(request()->query()))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with([
            'variants',
            'categories',
            'images' => function (Builder $query) {
                $query->where('is_saved', true)->orderBy('position', 'asc');
            }
            ])->firstOrFail();
        
        return new ProductResource($product);
    }
}
