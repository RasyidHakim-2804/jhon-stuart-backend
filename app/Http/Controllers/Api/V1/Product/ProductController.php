<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\Product\ProductResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Query\Product\SortByPrice;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lang = request()->getLanguages()[0];
        $products = QueryBuilder::for(Product::class)
                    ->with([
                        'images' => function (Builder $query) {
                            $query->where('is_saved', true)->orderBy('position', 'asc');
                        }
                    ])
                    ->defaultSort('name')
                    ->allowedSorts([
                        'name',
                        'id',
                        AllowedSort::custom('price', new SortByPrice)
                    ])
                    ->allowedFilters(['name'])
                    ->paginate(25)->appends(request()->query());

        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $lang = request()->getLanguages()[0];

        $product = Product::where('slug', $slug)->with([
            'variants',
            'categories',
            'translations' => function(Builder $query) use ($lang){
                $query->where('lang', $lang);
            },
            'images' => function (Builder $query) {
                $query->where('is_saved', true)->orderBy('position', 'asc');
            }
            ])->firstOrFail();
        
        return new ProductResource($product);
    }
}
