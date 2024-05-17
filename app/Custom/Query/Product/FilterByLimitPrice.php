<?php 
namespace App\Custom\Query\Product;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByLimitPrice implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if (is_array($value)) {
            $min = $value[0];
            $max = $value[1];
        } else {
            $min = $value;
            $max = null;
        }

        return $query->with([
            'variants' => function ($query) use ( $min, $max ) { // limit usd_price for variants
                $query->when($min !== null && $min >= 0, function($query) use ( $min ){
                    return $query->where('usd_price', '>=', $min);
                })
                ->when($max !== null && $max >= 0, function($query) use ( $max ){
                    return $query->where('usd_price', '<=', $max);
                });
            }]) 
            ->whereHas('variants', function($query) use ( $min, $max ) { //get prduct where variants limit
                $query->when($min !== null && $min >= 0, function($query) use ( $min ){
                    return $query->where('usd_price', '>=', $min);
                })
                ->when($max !== null && $max >= 0, function($query) use ( $max ){
                    return $query->where('usd_price', '<=', $max);
                });
            }
        );
    }
}