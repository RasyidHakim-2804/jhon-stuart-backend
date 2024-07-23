<?php

namespace App\Http\Controllers\Api\V1\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Event\CreateEventRequest;
use App\Http\Requests\Api\V1\Event\UpdateEventRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Api\V1\Event\EventResource;
use App\Models\Event;
use App\Models\Image\EventImage;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Support\Facades\DB;

class AdminEventController extends Controller
{
    public function index()
    {
        $events = Event::paginate(25);

        return EventResource::collection($events);
    }

    public function show(Event $event)
    {
        $event =$event->load(['images', 'author']);

        return new EventResource($event);
    }

    public function store(CreateEventRequest $request)
    {
        try {
            DB::beginTransaction();

            $request = $request->validated();
            $event   = new Event();

            $event->title       = $request['title'];
            $event->meta        = $request['meta'];
            $event->body        = $request['body'];
            $event->video_url   = $request['video_url'];
            $event->author_id   = $request['author_id'];
            $event->views       = 0;

            $event->save();
            $imageServices = [];
            $position      = 1;

            foreach ($request['images'] as $key => $image) {
                $eventImage   = new EventImage();
                $imageService = new ImageService($eventImage);
                
                $eventImage->position = $position;
                $eventImage->url      = $imageService->upload($image)->path;
                $eventImage->event_id = $event->id;
                $imageServices[]      = $imageService;

                $eventImage->save();

                $position++;
            }

            DB::commit();

            return new SuccessResource(201);

        } catch (\Throwable $th) {
            
            DB::rollBack();

            if (!empty($imageServices)) {
                foreach ($imageServices as $imageService) {
                    $imageService->rollback();
                }
            }
            
            throw $th;
        }
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        try {
            DB::beginTransaction();

            $request = $request->validated();

            $event->title       = $request['title'];
            $event->meta        = $request['meta'];
            $event->body        = $request['body'];
            $event->video_url   = $request['video_url'];
            $event->author_id   = $request['author_id'];

            $event->save();
            $imageServices = [];

            if (isset($request['images'])) {
                foreach ($request['images'] as $images) {
                    $eventImage   = $event->images()->where('position', $images['position'])->firstOrNew();
                    $imageService = new ImageService($eventImage);
                    
                    $eventImage->position = $images['position'];
                    $eventImage->url      = $imageService->update($images['image'])->path;
                    $eventImage->event_id = $event->id;
                    $imageServices[]      = $imageService;
    
                    $eventImage->save();
                }
            }            

            DB::commit();

            return new SuccessResource();

        } catch (\Throwable $th) {

            DB::rollBack();
            
            if (!empty($imageServices)) {
                foreach ($imageServices as $imageService) {
                    $imageService->rollback();
                }
            }

            throw $th;
        }
    }

    public function destroy(Event $event)
    {
        try {
            DB::beginTransaction();

            $imageServices = ImageService::collection($event->images);
            $imageServices->delete();
            $event->images()->delete();
            $event->delete();

            DB::commit();

            return new SuccessResource();

        } catch (\Throwable $th) {
            DB::rollBack();

            if (isset($imageServices)) {
                $imageServices->rollback();
            }
            
            throw $th;
        }   
    }
}
