<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleAuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('article_authors')->insert([
            'id'            => 1,
            'first_name'    => 'Zaher',
            'last_name'     => 'Maen',
            'image_url'     => 'https://source.unsplash.com/random/400x400',
        ]);

        DB::table('article_authors')->insert([
            'id'            => 2,
            'first_name'    => 'Indiana',
            'last_name'     => 'Jones',
            'image_url'     => 'https://source.unsplash.com/random/400x400',
        ]);

        DB::table('article_authors')->insert([
            'id'            => 3,
            'first_name'    => 'George',
            'last_name'     => 'Lucas',
            'image_url'     => 'https://source.unsplash.com/random/400x400',
        ]);
    }
}
