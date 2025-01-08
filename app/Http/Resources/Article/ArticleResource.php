<?php

namespace App\Http\Resources\Article;

use App\Http\Resources\Admin\AdminResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'title'         => ArticleTranslationResource::collection($this->titleTranslations),
            'type'          => $this->type,
            'video'         => new ArticleVideoResource($this->video),
            'body'          => ArticleTranslationResource::collection($this->bodyTranslations),
            'meta'          => ArticleTranslationResource::collection($this->metaTranslations),
            'total_view'    => $this->total_view,
            'thumbnail'     => $this->imageUrl('image'),
            'images'        => ArticleImageResource::collection($this->whenLoaded('images')),
            'author'        => new AdminResource($this->author),
            // 'comments'      => ArticleCommentResource::collection($this->comments),
            'created_at'    => date("d F Y", strtotime($this->created_at)),
            'updated_at'    => $this->updated_at,
        ];
    }
}
