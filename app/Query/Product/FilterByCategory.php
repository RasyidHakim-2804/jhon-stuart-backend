<?php 
namespace App\Query\Product;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByCategory implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if (!is_array($value)) {
            $categories_id[0] = $value;
        } else {
            $categories_id = $value;
        }

        if ($categories_id[0] != null) {
            $query->whereHas('categories', function($query) use ($categories_id) {
                        $query->select('product_id')
                        ->whereIn('category_id', $categories_id)
                        ->groupBy('product_id')
                        ->havingRaw('COUNT( DISTINCT category_id) = ' . count($categories_id));
                    });
        }
        return $query;
    }
}