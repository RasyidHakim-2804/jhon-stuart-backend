<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $product = Product::where('slug', '=', $slug)
            ->with([
                'variants',
                'category:id,name,slug',
                'images' => function ($query) {
                    $query->where('position', '!=', null);
                }
            ])
            ->available()
            ->firstOrFail();
        
        return new ProductResource($product);
    }

    public function moreProducts(ProductCategory $category)
    {
        $products = Product::available()
                    ->where('category_id', '=', $category->id)
                    ->with([
                        'images' => function ($query) {
                            $query->where('position', '=', 1);
                        },
                    ])
                    ->selectVariantPrice()
                    ->withMin('variants', 'usd_price')
                    ->orderBy('popular', 'desc')
                    ->paginate(3)->appends(request()->query());

        return ProductResource::collection($products);
    }

    public function index(ProductCategory $category)
    {
        $products = QueryBuilder::for(Product::class)
                    ->selectVariantPrice()
                    ->available()
                    ->where('category_id', $category->id)
                    ->with([
                        'thumbnailImage',
                    ])
                    ->defaultSort('price')
                    ->allowedSorts([
                        'popular',
                        'price'
                    ])
                    ->allowedFilters([
                        AllowedFilter::partial('search', 'name'),
                    ])
                    ->paginate(12)->appends(request()->query());

            return ProductResource::collection($products);
    }
}
