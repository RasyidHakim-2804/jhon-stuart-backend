<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Admin\AdminResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminEventResource extends JsonResource
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
            'image'         => $this->imageUrl('image'),
            // 'comments'      => EventCommentResource::collection($this->comments),
            'author'        => new AdminResource($this->author),
            'created_at'    => date("d F Y", strtotime($this->created_at)),
            'updated_at'    => $this->updated_at,
        ];
    }
}
