<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PivotProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pivot_product_category')->insert([
            'id'                    => 1,
            'product_id'            => 1,
            'product_categories_id' => 1
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 2,
            'product_id'            => 2,
            'product_categories_id' => 2
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 3,
            'product_id'            => 3,
            'product_categories_id' => 3
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 4,
            'product_id'            => 4,
            'product_categories_id' => 4
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 5,
            'product_id'            => 5,
            'product_categories_id' => 1
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 6,
            'product_id'            => 6,
            'product_categories_id' => 5
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 7,
            'product_id'            => 7,
            'product_categories_id' => 5
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 8,
            'product_id'            => 8,
            'product_categories_id' => 5
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 9,
            'product_id'            => 9,
            'product_categories_id' => 8
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 10,
            'product_id'            => 10,
            'product_categories_id' => 8
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 11,
            'product_id'            => 11,
            'product_categories_id' => 6
        ]);

        DB::table('pivot_product_category')->insert([
            'id'                    => 12,
            'product_id'            => 12,
            'product_categories_id' => 6
        ]);
    }
}
