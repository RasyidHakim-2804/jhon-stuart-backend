<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class PorductVariantSeeder extends Seeder
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
            'size'          => '8.5x6x7.5 cm/3.35x2.36x2.95 inches',
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
            'size'          => '8.5x6x7.5 cm/3.35x2.36x2.95 inches',
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
            'size'          => '8.5x6x7.5 cm/3.35x2.36x2.95 inches',
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
            'size'          => '8.5x6x7.5 cm/3.35x2.36x2.95 inches',
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
