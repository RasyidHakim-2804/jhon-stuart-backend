<?php

namespace App\Http\Resources\Api\V1\Event;

use App\Http\Resources\Api\V1\ArticleAuthor\ArticleAuthorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'title'         => $this->title,
            'body'          => $this->body,
            'meta'          => $this->meta,
            'author'        => new ArticleAuthorResource($this->whenLoaded('author')),
            'image'         => EventImageResource::collection($this->whenLoaded('images')),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
