<?php

namespace App\Http\Controllers\Api\V1\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Event\SaveEventImagesRequest;
use App\Http\Requests\Api\Event\StoreEventImagesRequest;
use App\Http\Resources\Event\EventImageResource;
use App\Models\Event;
use App\Models\EventImage;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminEventImagesController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('event_image');
    }

    public function index(Event $event)
    {
        $images = $event->images()->saved()->get();

        return EventImageResource::collection($images);
    }

    public function store(Event $event, StoreEventImagesRequest $request)
    {
        try {
            DB::beginTransaction();

            $eventImage = new EventImage();
            $imageService = new ImageService($eventImage->imagePath(), $request->image);
            $imageService->save();

            $eventImage->event_id  = $event->id;
            $eventImage->image       = $imageService->getImageName();
            $eventImage->position    = null;
            $eventImage->save();

            DB::commit();

            return $this->successStoreWithData(
                new EventImageResource($eventImage)
            );

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function saveImages(SaveEventImagesRequest $request, Event $event)
    {
        try {
            DB::beginTransaction();

            $imagesId       = $request->validated('images_id');
            $savedImages    = $event->images()->whereIn('id', $imagesId)->get();
            $unsavedImages  = $event->images()->whereNotIn('id', $imagesId)->get();

            $event->images()->whereNotIn('id', $imagesId)->delete();

            foreach ($imagesId as $position => $id) {
                $image           = $savedImages->where('id', '=', $id)->firstOrFail();
                $image->position = $position + 1;
                $image->save();
            }

            foreach ($unsavedImages as $unsavedImage) {
                $imageService = new ImageService($unsavedImage->imagePath());
                $imageService->delete($unsavedImage->image);
                $unsavedImage->delete();
            }

            DB::commit();

            return $this->successResponse();

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }

    }
}

