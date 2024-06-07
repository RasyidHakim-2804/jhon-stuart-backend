<?php

namespace App\Http\Resources\Api\V1\Article;

use App\Http\Resources\Api\V1\ArticleAuthor\ArticleAuthorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'category'      => $this->category->name,
            'title'         => $this->translations->first()->title,
            'body'          => $this->translations->first()->body,
            'author'        => new ArticleAuthorResource($this->author),
            'image'         => $this->image_url,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
