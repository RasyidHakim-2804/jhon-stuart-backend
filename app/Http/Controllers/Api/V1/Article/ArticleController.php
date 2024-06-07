<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Article\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $lang    = $request->lang;

        $article = Article::with([
            'author',
            'category',
            'translations' => function($query) use ($lang){
                $query->where('lang', $lang);
            }
        ])->paginate(25);

        return ArticleResource::collection($article);
    }

    public function show($slug, Request $request)
    {
        $lang    = $request->lang;

        $article = Article::where('slug', $slug)->with([
            'author',
            'category:id,name',
            'translations' => function($query) use ($lang){
                $query->where('lang', $lang);
            }
        ])->firstOrFail();

        $article->views++;
        $article->save();

        return new ArticleResource($article);
    }
}
