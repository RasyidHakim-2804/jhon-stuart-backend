<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
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
            'translate' => function($query) use ($lang){
                $query->where('lang', $lang);
            },
            'images' => function ($query) {
                $query->where('is_saved', true);
            }
            ])->firstOrFail();
        
        return new ProductResource($product);
    }

    public function moreProducts(ProductCategory $category)
    {
        $products = Product::query()
                    ->join('pivot_product_category', 'pivot_product_category.product_id', 'products.id')
                    ->where('is_saleable', '=', true)
                    ->where('pivot_product_category.product_categories_id', '=', $category->id)
                    ->with([
                        'images' => function ($query) {
                            $query->where([
                                [ 'is_saved', '=', true ],
                                [ 'position', '=', 1]
                            ]);
                        },
                        'minPriceVariant'
                    ])
                    ->withMin('variants', 'usd_price')
                    ->orderBy('popular', 'desc')
                    ->paginate(3)->appends(request()->query());

        return ProductResource::collection($products);
    }
}
