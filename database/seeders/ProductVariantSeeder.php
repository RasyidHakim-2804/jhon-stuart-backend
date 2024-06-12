<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            if ($product->is_varied) {
                $this->generateMultipleVariant($product);
            } else {
                $this->generateSingleVariant($product);
            }
            
        }
    }

    private function generateSingleVariant($product)
    {
        DB::table('product_variants')->insert([
            'name'          => $product->name,
            'is_default'    => true,
            'product_id'    => $product->id,
            'stock'         => rand(30, 120),
            'idr_price'     => rand(100000, 10000000),
            'usd_price'     => rand(100, 1000),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    private function generateMultipleVariant($product)
    {
        DB::table('product_variants')->insert([
            'name'          => 'Small',
            'is_default'    => false,
            'product_id'    => $product->id,
            'stock'         => rand(30, 120),
            'idr_price'     => rand(100000, 10000000),
            'usd_price'     => rand(100, 1000),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('product_variants')->insert([
            'name'          => 'Medium',
            'is_default'    => false,
            'product_id'    => $product->id,
            'stock'         => rand(30, 120),
            'idr_price'     => rand(100000, 10000000),
            'usd_price'     => rand(100, 1000),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('product_variants')->insert([
            'name'          => 'Large',
            'is_default'    => false,
            'product_id'    => $product->id,
            'stock'         => rand(30, 120),
            'idr_price'     => rand(100000, 10000000),
            'usd_price'     => rand(100, 1000),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    
}
