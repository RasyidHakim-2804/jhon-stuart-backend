<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Category\ProductCategory\ProductCategoryResource;

class ProductResource extends JsonResource
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
            'slug'          => $this->slug,
            'is_varied'     => $this->is_varied,
            'is_saleable'   => $this->is_saleable,

            $this->mergeWhen( $this->relationLoaded('translations'), [
                'description'       => $this->translations->first()->description,
            ]),

            $this->mergeWhen( $this->relationLoaded('categories'), [
                'category_type' => $this->categories->first()->type,
                'categories'    => ProductCategoryResource::collection($this->categories),    
            ]),

            'variants'      => ProductVariantResource::collection($this->whenLoaded('variants')), 
            'images'        => ProductImageResource::collection($this->whenLoaded('images')),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
