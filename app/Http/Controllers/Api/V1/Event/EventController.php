<?php

namespace App\Http\Controllers\Api\V1\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\Event\EventResource;
use App\Http\Resources\Event\ListEventResource;
use App\Models\Event;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EventController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        return ListEventResource::collection(
            QueryBuilder::for(Event::class)
            ->allowedFilters([
                AllowedFilter::scope('seacrh'),
            ])
            ->latest()->with([
                'metaTranslations'  => fn($translation) => $translation->where('language', config('app.locale')),
                'titleTranslations' => fn($translation) => $translation->where('language', config('app.locale')),
                'author'
            ])->paginate(15)
        );
    }

    public function show(Event $article)
    {
        $article->total_view = $article->total_view + 1;

        $article->save();

        return $this->successGet(new EventResource(
            $article->load([
                'metaTranslations'  => fn($translation) => $translation->where('language', config('app.locale')),
                'titleTranslations' => fn($translation) => $translation->where('language', config('app.locale')),
                'bodyTranslations'  => fn($translation) => $translation->where('language', config('app.locale')),
                'author',
            ])
        ));
    }

    public function top()
    {
        return ListEventResource::collection(
            QueryBuilder::for(Event::class)
            ->allowedFilters([
                AllowedFilter::scope('seacrh'),
            ])
            ->orderBy('total_view', 'DESC')->with([
                'metaTranslations'  => fn($translation) => $translation->where('language', config('app.locale')),
                'titleTranslations' => fn($translation) => $translation->where('language', config('app.locale')),
                'author'
            ])->limit(3)->get()
        );
    }
}
