<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //museum

        DB::table('products')->insert([
            'id'            => 1,
            'name'          => 'Jon Stuart Anderson Orb of Delight II 2024',
            'slug'          => Str::slug('Jon Stuart Anderson Orb of Delight II 2024'),
            'is_saleable'   => 1,
            'is_varied'     => 0,
            'popular'       => 100,
        ]);

        DB::table('products')->insert([
            'id'            => 2,
            'name'          => 'Jon Stuart Anderson Orb of Delight I 2024',
            'slug'          => Str::slug('Jon Stuart Anderson Orb of Delight I 2024'),
            'is_saleable'   => 1,
            'is_varied'     => 0,
            'popular'       => 70,
        ]);

        DB::table('products')->insert([
            'id'            => 3,
            'name'          => 'Jon Stuart Anderson Skull III 2024',
            'slug'          => Str::slug('Jon Stuart Anderson Skull III 2024'),
            'is_saleable'   => 1,
            'is_varied'     => 0,
            'popular'       => 65,
        ]);

        DB::table('products')->insert([
            'id'            => 4,
            'name'          => 'Jon Stuart Anderson Vase I 2024',
            'slug'          => Str::slug('Jon Stuart Anderson Vase I 2024'),
            'is_saleable'   => 1,
            'is_varied'     => 0,
            'popular'       => 80,
        ]);

        DB::table('products')->insert([
            'id'            => 5,
            'name'          => 'Jon Stuart Anderson Winged Dragon 2024',
            'slug'          => Str::slug('Jon Stuart Anderson Winged Dragon 2024'),
            'is_saleable'   => 1,
            'is_varied'     => 0,
            'popular'       => 90,
        ]);

        //gallery
        DB::table('products')->insert([
            'id'            => 6,
            'name'          => 'Jon Stuart Anderson Wolf Family 2023',
            'slug'          => Str::slug('Jon Stuart Anderson Wolf Family 2023'),
            'is_saleable'   => 1,
            'is_varied'     => 1,
            'popular'       => 100,
        ]);

        DB::table('products')->insert([
            'id'            => 7,
            'name'          => 'Jon Stuart Anderson Tyrannosaurus Desk II',
            'slug'          => Str::slug('Jon Stuart Anderson Tyrannosaurus Desk II'),
            'is_saleable'   => 1,
            'is_varied'     => 1,
            'popular'       => 10,
        ]);

        DB::table('products')->insert([
            'id'            => 8,
            'name'          => 'Jon Stuart Anderson Horned Toad Jumbo 2023',
            'slug'          => Str::slug('Jon Stuart Anderson Horned Toad Jumbo 2023'),
            'is_saleable'   => 1,
            'is_varied'     => 1,
            'popular'       => 76,
        ]);

        DB::table('products')->insert([
            'id'            => 9,
            'name'          => 'Glacial Triangle',
            'slug'          => Str::slug('Glacial Triangle'),
            'is_saleable'   => 1,
            'is_varied'     => 1,
            'popular'       => 100,
        ]);

        DB::table('products')->insert([
            'id'            => 10,
            'name'          => 'Jaguar Pendant, Brooch',
            'slug'          => Str::slug('Jaguar Pendant, Brooch'),
            'is_saleable'   => 1,
            'is_varied'     => 1,
            'popular'       => 103,
        ]);

        DB::table('products')->insert([
            'id'            => 11,
            'name'          => 'Jon Stuart Anderson Vase III 2023',
            'slug'          => Str::slug('Jon Stuart Anderson Vase III 2023'),
            'is_saleable'   => 1,
            'is_varied'     => 0,
            'popular'       => 10,
        ]);

        DB::table('products')->insert([
            'id'            => 12,
            'name'          => 'Jon Stuart Anderson Rectangular Frog Bowl',
            'slug'          => Str::slug('Jon Stuart Anderson Rectangular Frog Bowl'),
            'is_saleable'   => 1,
            'is_varied'     => 0,
            'popular'       => 10,
        ]);
    }
}
