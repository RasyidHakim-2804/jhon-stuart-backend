<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTranslationsSeeder extends Seeder
{
    
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product ) {
            $this->generateEnTransalation($product->id);
            $this->generateIdTransalation($product->id);
        }
    }

    private function generateEnTransalation($product_id)
    {
        DB::table('product_translations')->insert([
            'product_id'    => $product_id,
            'lang'          => 'en',
            'description'   => fake('en_GB')->paragraph(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    private function generateIdTransalation($product_id)
    {
        DB::table('product_translations')->insert([
            'product_id'    => $product_id,
            'lang'          => 'id',
            'description'   => fake('id_ID')->paragraph(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
