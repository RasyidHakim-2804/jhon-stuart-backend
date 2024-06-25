<?php

namespace App\Http\Controllers\Api\V1\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductCategory\ProductCategoryResource;
use App\Models\ProductCategory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\Product\ProductResource;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->get('lang');
        $categories = QueryBuilder::for(ProductCategory::class)
                        ->with([
                            'translate' => function($query) use ($lang) {
                                $query->where('lang', $lang);
                            }
                        ])
                        ->allowedFilters([
                            AllowedFilter::exact('type')
                        ])
                        ->paginate(25);
    
        return ProductCategoryResource::collection($categories);
    }

    public function show(ProductCategory $category, Request $request)
    {
        $products = QueryBuilder::for(Product::class)
                    ->join('pivot_product_category', 'pivot_product_category.product_id', 'products.id')
                    ->where('is_saleable', '=', true)
                    ->where('pivot_product_category.product_categories_id', '=', $category->id)
                    ->with([
                        'images' => function ($query) {
                            $query->where('is_saved', '=', true );
                        },
                        'minPriceVariant'
                    ])
                    ->withMin('variants', 'usd_price')
                    ->defaultSort('variants_min_usd_price') // this column is from withMin('variants', 'usd_price
                    ->allowedSorts([
                        'name',
                        'id',
                        'popular',
                        AllowedSort::field('price', 'variants_min_usd_price') // this column is from withMin('variants', 'usd_price')
                    ])
                    ->allowedFilters([
                        AllowedFilter::partial('search', 'name'),
                    ])
                    ->paginate(12)->appends(request()->query());

            return ProductResource::collection($products);
    }
}
