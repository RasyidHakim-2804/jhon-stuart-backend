<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('article_images')->insert([
            'id'            => 1,
            'article_id'    => 4,
            'image'         => 'https://picsum.photos/500/300',
            'created_at'    => now(),
        ]);

        DB::table('article_images')->insert([
            'id'            => 2,
            'article_id'    => 4,
            'image'         => 'https://picsum.photos/500/300',
            'created_at'    => now(),
        ]);

        DB::table('article_images')->insert([
            'id'            => 3,
            'article_id'    => 4,
            'image'         => 'https://picsum.photos/500/300',
            'created_at'    => now(),
        ]);

        DB::table('article_images')->insert([
            'id'            => 4,
            'article_id'    => 4,
            'image'         => 'https://picsum.photos/500/300',
            'created_at'    => now(),
        ]);

        DB::table('article_images')->insert([
            'id'            => 5,
            'article_id'    => 4,
            'image'         => 'https://picsum.photos/500/300',
            'created_at'    => now(),
        ]);
    }
}
