<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Article\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $article = Article::exceptBody()->with([
            'author',
            'category:id,name',
        ])->paginate(25);

        return ArticleResource::collection($article);
    }

    public function show(Article $article)
    {
        $article = $article->load([
            'author',
            'category:id,name',
        ]);

        $article->views++;
        $article->save();

        return new ArticleResource($article);
    }

    public function topArticles()
    {
        $topArticles = Article::with([
            'author',
            'category:id,name',
        ])->orderBy('views', 'asc')->take(10)->get();

        return ArticleResource::collection($topArticles);
    }
}
