<?php 
namespace App\Query\Product;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

/**
 * this query used for search products with multiple categories
 */
class FilterByCategory implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if (!is_array($value)) {
            $categories_id[0] = $value;
        } else {
            $categories_id = $value;
        }

        return $query->whereHas('categories', function($query) use ($categories_id) {
            $query->select('product_id')
            ->whereIn('product_categories_id', $categories_id)
            ->groupBy('product_id')
            ->havingRaw('COUNT( DISTINCT product_categories_id) = ' . count($categories_id));
        });
    }
}