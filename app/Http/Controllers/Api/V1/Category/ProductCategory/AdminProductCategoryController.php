<?php

namespace App\Http\Controllers\Api\V1\Category\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Category\ProductCategory\ProductCategoryRequest;
use App\Http\Resources\Api\V1\Category\ProductCategory\ProductCategoryResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminProductCategoryController extends Controller
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

    public function store(ProductCategoryRequest $request)
    {
        $category       = new ProductCategory();
        $category->name = $request->name;
        $category->type = $request->type;

        $category->save();

        return new SuccessResource(201);
    }

    public function show(ProductCategory $category)
    {
        return new ProductCategoryResource($category);
    }

    public function update(ProductCategoryRequest $request, ProductCategory $category)
    {
        $category->type = $request->type;
        $category->name = $request->name;

        $category->save();

        return new SuccessResource();
    }

    public function destroy(ProductCategory $category)
    {
        DB::transaction(function() use ($category)
        {
            $category->products()->detach();
            $category->delete();    
        });
        
        return new SuccessResource();
    }
}
