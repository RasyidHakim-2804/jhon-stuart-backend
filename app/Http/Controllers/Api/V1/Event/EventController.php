<?php

namespace App\Http\Controllers\Api\V1\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Event\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::paginate(25);

        return EventResource::collection($events);
    }

    public function show(Event $event)
    {
        $event->views++;
        $event->save();
        
        $event =$event->load(['images', 'author']);
        
        return new EventResource($event);
    }

    public function topEvents()
    {
        $topArticles = Event::with([
            'author',
            'images'
        ])->orderBy('views', 'asc')->take(5)->get();

        return EventResource::collection($topArticles);
    }
}
