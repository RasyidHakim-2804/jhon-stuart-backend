<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleVideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('article_videos')->insert([
            'id'            => 1,
            'article_id'    => 4,
            'video'         => 'https://www.youtube.com/watch?v=5NzfqW_Yt6Y&list=RDMMgFTicEWzKks&index=12',
            'created_at'    => now(),
        ]);
    }
}
