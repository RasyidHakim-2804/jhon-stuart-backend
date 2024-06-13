<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\ProductCategory\ProductCategoryResource;

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
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'is_varied'       => $this->is_varied,
            'is_saleable'     => $this->is_saleable,

            $this->mergeWhen( !$this->relationLoaded('minPricevariant'), function() {
                return [
                    'prices' => [
                        'idr_price' => $this->minPricevariant->idr_price,
                        'usd_price' => $this->minPricevariant->usd_price,
                    ],
                ];
            }),

            'description'   => $this->whenLoaded('translations', function () use ($request){
                return $this->translate($request->lang)->description;
            }),

            'categories'      => ProductCategoryResource::collection($this->whenLoaded('categories')),
            'variants'        => ProductVariantResource::collection($this->whenLoaded('variants')), 
            'images'          => ProductImageResource::collection($this->whenLoaded('images')),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
