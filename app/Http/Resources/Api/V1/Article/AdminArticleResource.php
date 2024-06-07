<?php

namespace App\Http\Resources\Api\V1\Article;

use App\Http\Resources\Api\V1\ArticleAuthor\ArticleAuthorResource;
use App\Http\Resources\Api\V1\ArticleCategory\ArticleCategoryResource;
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
            'image'         => $this->image_url,
            'views'         => $this->views,
            'translation'   => ArticleTranslationResource::collection($this->whenLoaded('translations')),
            'author'        => new ArticleAuthorResource($this->whenLoaded('author')),
            'category'      => new ArticleCategoryResource($this->whenLoaded('category')),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
