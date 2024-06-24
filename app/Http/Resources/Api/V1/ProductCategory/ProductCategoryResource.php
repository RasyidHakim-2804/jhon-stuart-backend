<?php

namespace App\Http\Resources\Api\V1\ProductCategory;

use App\Http\Resources\Api\V1\Product\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
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
            'name'          => $this->name,
            'type'          => $this->type,
            'slug'          => $this->slug,
            $this->mergeWhen($this->relationLoaded('translate'), function() {
                return [
                    'description' => $this->translate->description,
                ];
            }),
            'description'   => ProductCategoryTranslationResource::collection($this->whenLoaded('translations')),
            'products'      => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
