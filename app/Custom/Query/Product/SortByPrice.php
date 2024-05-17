<?php 
namespace App\Custom\Query\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;

class SortByPrice implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending? 'DESC' : 'ASC';

        $query->select('products.*', 'variants.min_price')
        ->rightJoinSub(
            $this->variantMinPrice(),
            'variants',
            function (JoinClause $join) {
                $join->on('products.id', '=', 'variants.product_id');
            }
        )
        ->orderBy('variants.min_price', $direction);
    }

    private function variantMinPrice()
    {
        return DB::table('product_variants')
                ->select(
                    'product_variants.product_id',
                    DB::raw("MIN(`product_variants`.`usd_price`) AS `min_price`"))
                ->groupBy('product_id');
    }
}