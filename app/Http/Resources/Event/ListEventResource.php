<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Admin\AdminResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ListEventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'title'         => $this->titleTranslations->first()?->translation,
            'type'          => $this->type,
            'meta'          => $this->metaTranslations->first()?->translation,
            'total_view'    => $this->total_view,
            'images'        => EventImageResource::collection($this->whenLoaded('images')),
            'author'        => new AdminResource($this->author),
            'created_at'    => date("d F Y", strtotime($this->created_at)),
            'updated_at'    => $this->updated_at,
        ];
    }
}
