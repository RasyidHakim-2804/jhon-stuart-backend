<?php

namespace App\Http\Controllers\Api\V1\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProductCategory\ProductCategoryRequest;
use App\Http\Resources\Api\V1\ProductCategory\ProductCategoryResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\ProductCategory;
use App\Models\Translation\ProductCategoryTranslation;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminProductCategoryController extends Controller
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

    public function store(ProductCategoryRequest $request)
    {
        DB::transaction(function() use ($request) {
            $request        = $request->validated();
            $category       = new ProductCategory();
            $category->name = $request['name'];
            $category->type = $request['type'];
    
            $category->save();
    
            foreach ( $request['description'] as $lang => $value) {
                $this->setTranslation($request, $lang, $category->id);
            }    
        });

        return new SuccessResource(201);
    }

    public function show(ProductCategory $category)
    {
        $category = $category->load('translations');
        
        return new ProductCategoryResource($category);
    }

    public function update(ProductCategoryRequest $request, ProductCategory $category)
    {
        DB::transaction(function() use ($request, $category) {
            $request        = $request->validated();
            $category->type = $request['type'];
            $category->name = $request['name'];
    
            $category->save();
    
            foreach ( $request['description'] as $lang => $value) {
                $this->setTranslation($request, $lang, $category->id);
            }     
        });

        return new SuccessResource();
    }

    public function destroy(ProductCategory $category)
    {
        DB::transaction(function() use ($category)
        {
            $category->translations()->delete();
            $category->products()->detach();
            $category->delete();    
        });
        
        return new SuccessResource();
    }

    //////////////////////
    // private functions//
    //////////////////////

    private function setTranslation(array $request, $lang, $product_category_id)
    {
        $translation = ProductCategoryTranslation::query()
                        ->where('product_category_id', $product_category_id)
                        ->where('lang', $lang)
                        ->firstOrNew();
        
        $translation->product_category_id    = $product_category_id;
        $translation->lang                   = $lang;
        $translation->description            = $request['description'][$lang];
        $translation->save();
    }
}
