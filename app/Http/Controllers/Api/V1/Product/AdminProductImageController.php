<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\Image\CreateImageRequest;
use App\Http\Requests\Api\V1\Product\Image\SaveImageRequest;
use App\Http\Requests\Api\V1\Product\Image\StoreImageRequest;
use App\Http\Resources\Api\V1\Product\ProductImageResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\Product;
use App\Models\Image\ProductImage;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Support\Facades\DB;

class AdminProductImageController extends Controller
{
    public function index(Product $product)
    {
        $images = $product->images;

        return ProductImageResource::collection($images);
    }

    public function store(Product $product,StoreImageRequest $request)
    {
        $lastPosition       = ProductImage::where('product_id', $product->id)
                                ->orderByDesc('id')
                                ->value('position');

        $image              = new ProductImage();
        $imageService       = new ImageService($image);
        $result             = $imageService->upload($request->images);            
    
        $image->product_id  = $product->id;
        $image->name        = $result->name;
        $image->path        = $result->path;
        $image->position    = $lastPosition + 1;
        $image->is_saved    = false;
        $image->save();

        return new ProductImageResource($image);
    }

    public function saveImages(SaveImageRequest $request, Product $product)
    {
        $imagesid     = $request->validated();
        $position     = 1;
        $undeletedIds = [];

        foreach ($imagesid as $key => $id) {
            $image           = ProductImage::findOrFail($id);
            $image->is_saved = true;
            $image->position = $position;
            $image->save();

            $position++;
            $undeletedIds[] = $id;
        }

        ProductImage::where('product_id', $product->id)
        ->whereNotIn('id', $undeletedIds)
        ->delete();

        return new SuccessResource();
    }

    public function destroy(Product $product, ProductImage $image)
    {

    }
}
