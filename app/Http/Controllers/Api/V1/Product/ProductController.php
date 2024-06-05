<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Query\Product\FilterByCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Query\Product\SortByPrice;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = QueryBuilder::for(Product::class)
                    ->with([
                        'images' => function (Builder $query) {
                            $query->where('is_saved', true);
                        },
                    ])
                    ->withMin('variants', 'usd_price')
                    ->defaultSort('name')
                    ->allowedSorts([
                        'name',
                        'id',
                        'popular',
                        AllowedSort::custom('price', new SortByPrice)
                    ])
                    ->allowedFilters([
                        'name',
                        AllowedFilter::custom('category', new FilterByCategory)
                    ])
                    ->paginate(25)->appends(request()->query());

        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug, Request $request)
    {
        $lang = $request->lang;
        
        $product = Product::where('slug', $slug)->with([
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
}
