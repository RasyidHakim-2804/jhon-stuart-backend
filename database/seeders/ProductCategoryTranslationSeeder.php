<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoryTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ProductCategory::all();

        foreach ($categories as $category ) {
            $this->generateEnTransalation($category->id);
            $this->generateIdTransalation($category->id);
        }
    }

    private function generateEnTransalation($category_id)
    {
        DB::table('product_category_translations')->insert([
            'product_category_id'    => $category_id,
            'lang'          => 'en',
            'description'   => fake('en_GB')->paragraph(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    private function generateIdTransalation($category_id)
    {
        DB::table('product_category_translations')->insert([
            'product_category_id'    => $category_id,
            'lang'          => 'id',
            'description'   => fake('id_ID')->paragraph(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
