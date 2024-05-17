<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Query\Product\FilterByLimitPrice;
use App\Query\Product\FilterByCategory;
use App\Query\Product\SortByPrice;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\Product\ProductResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
        // dd(count(request()->query()));
        if (count(request()->query()) != 0) {
            
            return $this->filter();
        }
        $products = Product::with(['images' => function (Builder $query) {
            $query->where('is_saved', true)->orderBy('position', 'asc');
        }])->paginate(25);

        return ProductResource::collection($products);
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

    ////////////////////
    // private method //
    ////////////////////

    private function filter()
    {
        $products = QueryBuilder::for(Product::class)
                    ->with(['variants','images', 'categories'])
                    ->defaultSort('name')
                    ->allowedSorts([
                        'name',
                        'id',
                        AllowedSort::custom('price', new SortByPrice)
                    ])
                    ->allowedFilters([
                        AllowedFilter::custom('limit_price', new FilterByLimitPrice),
                        AllowedFilter::custom('categories',new FilterByCategory),
                    ])
                    ->paginate(25)->appends(request()->query());

                    // dd($products->toSql());
        return ProductResource::collection($products);
    }
}
