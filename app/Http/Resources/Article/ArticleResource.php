<?php

namespace App\Http\Resources\Article;

use App\Http\Resources\ArticleAuthor\ArticleAuthorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'category'      => $this->category->name,
            'title'         => $this->title,
            'body'          => $this->body,
            'meta'          => $this->meta,
            'author'        => new ArticleAuthorResource($this->author),
            'image'         => $this->image_url,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
