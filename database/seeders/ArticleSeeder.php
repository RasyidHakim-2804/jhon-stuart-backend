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
        // type-blog
        DB::table('articles')->insert([
            'id'            => 1,
            'type'          => 'blog',
            'title'         => 'Polymer Art Magazines',
            'slug'          => Str::slug('Polymer Art Magazines'),
            'views'         => rand(50, 100),
            'image'         => 'https://picsum.photos/500/300',
            'category_id'   => 1,
            'admin_id'      => 1,
            'created_at'    => now(),
        ]);

        DB::table('articles')->insert([
            'id'            => 2,
            'type'          => 'blog',
            'title'         => 'Dramatic Color Blends for Canes',
            'slug'          => Str::slug('Dramatic Color Blends for Canes'),
            'views'         => rand(50, 100),
            'image'         => 'https://picsum.photos/500/300',
            'category_id'   => 1,
            'admin_id'      => 1,
            'created_at'    => now(),
        ]);

        DB::table('articles')->insert([
            'id'            => 3,
            'type'          => 'blog',
            'title'         => 'Painting with Polymer clay',
            'slug'          => Str::slug('Painting with Polymer clay'),
            'views'         => rand(50, 100),
            'image'         => 'https://picsum.photos/500/300',
            'category_id'   => 1,
            'admin_id'      => 1,
            'created_at'    => now(),
        ]);

        //type - event
        DB::table('articles')->insert([
            'id'            => 4,
            'type'          => 'event',
            'title'         => 'Cane making tutorial 2013, Ubud, Bali',
            'slug'          => Str::slug('Cane making tutorial 2013, Ubud, Bali'),
            'views'         => rand(50, 100),
            // 'image'         => 'https://picsum.photos/500/300',
            'category_id'   => 1,
            'admin_id'      => 1,
            'created_at'    => now(),
        ]);
    }
}
