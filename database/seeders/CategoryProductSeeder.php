<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = DB::table('categories')->select('id')->get()->toArray();
        $productIds  = DB::table('products')->select('id')->get()->toArray();

        foreach ($productIds as $key => $value) {
            for ($i=0; $i < rand(2, 5); $i++) { 
                DB::table('category_product')->insert([
                    'category_id'   => $categoryIds[array_rand($categoryIds)]->id,
                    'product_id'    => $value->id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
