<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Product\ProductResource;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                        'images' => function ($query) {
                            $query->where('position', '!=', null);
                        }
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
        $user_id = 0;

        if ($user = auth()->guard('api_user')->user()) {
            $user_id = $user->id;
        }

        $product = Product::where('slug', '=', $slug)
            ->with([
                'variants'  => fn($query) => $query->inWishlist($user_id)->active(),
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
                        'images' => function ($query) {
                            $query->where('position', '!=', null);
                        }
                    ])
                    ->selectVariantPrice()
                    ->withMin('variants', 'usd_price')
                    ->orderBy('popular', 'desc')
                    ->paginate(3)->appends(request()->query());

        return ProductResource::collection($products);
    }
}
