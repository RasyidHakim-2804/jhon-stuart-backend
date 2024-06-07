<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('articles')->insert([
            'id'            => 1,
            'name'          => 'Polymer Art Magazines',
            'slug'          => Str::slug('Polymer Art Magazines'),
            'views'         => rand(50, 100),
            'image_url'     => 'https://source.unsplash.com/random/500x500',
            'category_id'   => 1,
            'author_id'     => 1,
            'created_at'    => now(),
        ]);

        DB::table('articles')->insert([
            'id'            => 2,
            'name'          => 'Dramatic Color Blends for Canes',
            'slug'          => Str::slug('Dramatic Color Blends for Canes'),
            'views'         => rand(50, 100),
            'image_url'     => 'https://source.unsplash.com/random/500x500',
            'category_id'   => 2,
            'author_id'     => 2,
            'created_at'    => now(),
        ]);

        DB::table('articles')->insert([
            'id'            => 3,
            'name'          => 'Painting with Polymer clay',
            'slug'          => Str::slug('Painting with Polymer clay'),
            'views'         => rand(50, 100),
            'image_url'     => 'https://source.unsplash.com/random/500x500',
            'category_id'   => 3,
            'author_id'     => 3,
            'created_at'    => now(),
        ]);
    }
}
