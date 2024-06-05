<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //type museum

        DB::table('product_categories')->insert([
            'id'         => 1,
            'name'       => 'One-off',
            'slug'       => Str::slug('one off'),
            'type'       => 'museum',
            'created_at' => now(),
        ]);

        DB::table('product_categories')->insert([
            'id'         => 2,
            'name'       => 'Limited',
            'slug'       => Str::slug('limited'),
            'type'       => 'museum',
            'created_at' => now(),
        ]);

        DB::table('product_categories')->insert([
            'id'         => 3,
            'name'       => 'Seasons',
            'slug'       => Str::slug('seasons'),
            'type'       => 'museum',
            'created_at' => now(),
        ]);

        DB::table('product_categories')->insert([
            'id'         => 4,
            'name'       => 'Year',
            'slug'       => Str::slug('year'),
            'type'       => 'museum',
            'created_at' => now(),
        ]);

        //type gallery

        DB::table('product_categories')->insert([
            'id'         => 5,
            'name'       => 'ANIMALS',
            'slug'       => Str::slug('animals'),
            'type'       => 'gallery',
            'created_at' => now(),
        ]);

        DB::table('product_categories')->insert([
            'id'         => 6,
            'name'       => 'VESSELS',
            'slug'       => Str::slug('vessels'),
            'type'       => 'gallery',
            'created_at' => now(),
        ]);

        DB::table('product_categories')->insert([
            'id'         => 7,
            'name'       => 'RAW CANES',
            'slug'       => Str::slug('raw canes'),
            'type'       => 'gallery',
            'created_at' => now(),
        ]);

        DB::table('product_categories')->insert([
            'id'         => 8,
            'name'       => 'JEWELLERY',
            'slug'       => Str::slug('jewellery'),
            'type'       => 'gallery',
            'created_at' => now(),
        ]);
    }
}
