<?php

namespace App\Http\Controllers\Api\V1\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Article\ArticleResource;
use App\Http\Resources\Article\ListArticleResource;
use App\Models\Article;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        return ListArticleResource::collection(
            QueryBuilder::for(Article::class)
            ->allowedFilters([
                'type'
            ])
            ->latest()->with([
                'metaTranslations'  => fn($translation) => $translation->where('language', config('app.locale')),
                'titleTranslations' => fn($translation) => $translation->where('language', config('app.locale')),
                'author',
                'category',
            ])->paginate(15)
        );
    }

    public function show(Article $article)
    {
        $article->total_view = $article->total_view + 1;

        $article->save();

        return $this->successGet(new ArticleResource($article->load([
            'metaTranslations'  => fn($translation) => $translation->where('language', config('app.locale')),
            'titleTranslations' => fn($translation) => $translation->where('language', config('app.locale')),
            'bodyTranslations'  => fn($translation) => $translation->where('language', config('app.locale')),
            'author',
            'category',
        ])));
    }

    public function top()
    {
        return ListArticleResource::collection(
            QueryBuilder::for(Article::class)
            ->allowedFilters([
                'type'
            ])
            ->orderBy('total_view', 'DESC')->with([
                'metaTranslations'  => fn($translation) => $translation->where('language', config('app.locale')),
                'titleTranslations' => fn($translation) => $translation->where('language', config('app.locale')),
                'author',
                'category',
            ])->limit(3)->get()
        );
    }
}
