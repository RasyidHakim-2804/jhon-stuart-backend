<?php

namespace App\Http\Controllers\Api\V1\ProductCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductCategory\ProductCategoryResource;
use App\Models\ProductCategory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Product;
use Illuminate\Http\Request;
use Psy\Command\WhereamiCommand;

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
}
