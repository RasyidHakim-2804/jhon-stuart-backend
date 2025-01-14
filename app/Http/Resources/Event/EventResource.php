<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Admin\AdminResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'title'         => EventTranslationResource::collection($this->titleTranslations),
            'type'          => $this->type,
            'video'         => $this->video,
            'body'          => EventTranslationResource::collection($this->bodyTranslations),
            'meta'          => EventTranslationResource::collection($this->metaTranslations),
            'total_view'    => $this->total_view,
            'thumbnail'     => $this->imageUrl('image'),
            'images'        => EventImageResource::collection($this->whenLoaded('images')),
            'author'        => new AdminResource($this->author),
            // 'comments'      => EventCommentResource::collection($this->comments),
            'created_at'    => date("d F Y", strtotime($this->created_at)),
            'updated_at'    => $this->updated_at,
        ];
    }
}
