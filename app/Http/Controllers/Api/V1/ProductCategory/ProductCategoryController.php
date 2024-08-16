<?php

namespace App\Http\Controllers\Api\V1\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategory\ProductCategoryResource;
use App\Models\ProductCategory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = QueryBuilder::for(ProductCategory::class)
                        ->allowedFilters([
                            AllowedFilter::exact('type')
                        ])
                        ->paginate(25);
    
        return ProductCategoryResource::collection($categories);
    }
}
