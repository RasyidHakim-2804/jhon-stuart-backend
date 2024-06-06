<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = Article::all();

        foreach ($articles as $article ) {
            DB::table('article_translations')->insert([
                'article_id'    => $article->id,
                'lang'          => 'en',
                'title'         => $article->name,
                'body'          => fake()->paragraph(),
                'created_at'    => now(),
            ]);

            DB::table('article_translations')->insert([
                'article_id'    => $article->id,
                'lang'          => 'id',
                'title'         => $article->name,
                'body'          => fake()->paragraph(),
                'created_at'    => now(),
            ]);
        }
    }
}
