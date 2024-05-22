<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Article\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $lang = request()->getLanguages();
        $article = Article::with([
            'image',
            'translation' => function($query) use ($lang){
                $query->where('lang', $lang);
            }
        ])->paginate(25);

        return ArticleResource::collection($article);
    }

    public function show($slug)
    {
        $lang = request()->getLanguages();
        $article = Article::where('slug', $slug)->with([
            'image',
            'translation' => function($query) use ($lang){
                $query->where('lang', $lang);
            }
        ])->firstOrFail();

        return new ArticleResource($article);
    }
}
