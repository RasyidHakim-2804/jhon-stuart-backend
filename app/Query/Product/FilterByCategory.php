<?php 
namespace App\Query\Product;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByCategory implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if (!is_array($value)) {
            $categories[0] = $value;
        } else {
            $categories = $value;
        }

        if ($categories[0] != null) {
            return $query->with('categories')
                    ->whereHas('categories', function($query) use ($categories) {
                        $query->whereIn('category_id', $categories);
                    }, '=', count($categories));
        }
        return $query;
    }
}