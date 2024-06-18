<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\Product\ProductResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = QueryBuilder::for(Product::class)
                    ->where('is_saleable', '=', true)
                    ->with([
                        'images' => function (Builder $query) {
                            $query->where([
                                [ 'is_saved', '=', true ],
                                [ 'position', '=', 1]
                            ]);
                        },
                        'minPriceVariant'
                    ])
                    ->withMin('variants', 'usd_price')
                    ->defaultSort('name')
                    ->allowedSorts([
                        'name',
                        'id',
                        'popular',
                        AllowedSort::field('price', 'variants_min_usd_price') // this column is from withMin('variants', 'usd_price')
                    ])
                    ->join('pivot_product_category', 'pivot_product_category.product_id', 'products.id')
                    ->allowedFilters([
                        AllowedFilter::partial('search', 'name'),
                        AllowedFilter::exact('category','pivot_product_category.product_categories_id')
                    ])
                    ->paginate(12)->appends(request()->query());

        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug, Request $request)
    {
        $lang = $request->lang;
        
        $product = Product::where([
                ['slug', '=', $slug],
                ['is_saleable', '=', true]
            ])->with([
            'variants',
            'categories',
            'translations' => function(Builder $query) use ($lang){
                $query->where('lang', $lang);
            },
            'images' => function (Builder $query) {
                $query->where('is_saved', true);
            }
            ])->firstOrFail();
        
        return new ProductResource($product);
    }

    public function moreProducts(Request $request)
    {
        $products = QueryBuilder::for(Product::class)
                    ->with([
                        'images' => function (Builder $query) {
                            $query->where([
                                [ 'is_saved', '=', true ],
                                [ 'position', '=', 1]
                            ]);
                        },
                        'minPriceVariant'
                    ])
                    ->withMin('variants', 'usd_price')
                    ->defaultSort('-popular')
                    ->join('pivot_product_category', 'pivot_product_category.product_id', 'products.id')
                    ->allowedFilters([
                        AllowedFilter::exact('category','pivot_product_category.product_categories_id')
                    ])
                    ->paginate(3)->appends(request()->query());

        return ProductResource::collection($products);
    }
}
