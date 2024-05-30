<?php

namespace App\Http\Controllers\Api\V1\Category\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Category\ProductCategory\ProductCategoryResource;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\ProductCategory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Product;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = QueryBuilder::for(ProductCategory::class)
                        ->allowedFilters([
                            AllowedFilter::exact('type')->default('museum')
                        ])
                        ->paginate(25);
    
        return ProductCategoryResource::collection($categories);
    }
}
