<?php 
namespace App\Service\Services\SearchServices;

use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\Product;
use App\Query\Product\FilterByLimitPrice;
use App\Query\Product\FilterByCategory;
use App\Query\Product\SortByPrice;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ProductSearchService
{
    public function __construct(private array $query)
    {
        
    }

    public function response()
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
                    ->paginate(25)->appends($this->query);

        return ProductResource::collection($products);
    }
}