<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Product\ProductResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    public function index(ProductCategory $category)
    {
        $products = QueryBuilder::for(Product::class)
                    ->selectVariantPrice()
                    ->active()
                    ->where('category_id', $category->id)
                    ->with([
                        'thumbnailImage',
                    ])
                    ->defaultSort('price')
                    ->allowedSorts([
                        'popular',
                        'price',
                        'rating',
                    ])
                    ->allowedFilters([
                        AllowedFilter::partial('search', 'name'),
                    ])
                    ->paginate(12)->appends(request()->query());

            return ProductResource::collection($products);
    }

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
            ->active()
            ->firstOrFail();

        return new ProductResource($product);
    }

    public function moreProducts(ProductCategory $category)
    {
        $products = Product::active()
                    ->where('category_id', '=', $category->id)
                    ->with([
                        'thumbnailImage'
                    ])
                    ->selectVariantPrice()
                    ->withMin('variants', 'usd_price')
                    ->orderBy('popular', 'desc')
                    ->paginate(3)->appends(request()->query());

        return ProductResource::collection($products);
    }
}
