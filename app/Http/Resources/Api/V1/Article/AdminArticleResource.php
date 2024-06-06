<?php

namespace App\Http\Resources\Api\V1\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminArticleResource extends JsonResource
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
            'translation'   => ArticleTranslationResource::collection($this->translation),
            'image'         => $this->image_url,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
