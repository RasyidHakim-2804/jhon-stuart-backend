<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
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
        $image              = new ProductImage();

        DB::transaction(function() use ($product,$request, $image)
        {
            $lastPosition       = ProductImage::where('product_id', $product->id)
            ->orderByDesc('id')
            ->value('position');

            $imageService       = new ImageService($image);
            $result             = $imageService->upload($request->images);            

            $image->product_id  = $product->id;
            $image->name        = $result->name;
            $image->path        = $result->path;
            $image->position    = $lastPosition + 1;
            $image->is_saved    = false;
            $image->save();
        });

        return new ProductImageResource($image);
    }

    public function saveImages(SaveImageRequest $request, Product $product)
    {
        DB::transaction(function() use ($request, $product)
        {
            $imagesid     = $request->validated()['images_id'];
            $position     = 1;
            $undeletedIds = [];

            if ( count($imagesid) != 0 ) {
                foreach ( $imagesid as $key => $id ) {
                    $image           = ProductImage::findOrFail($id);
                    $image->is_saved = true;
                    $image->position = $position;
                    $image->save();
        
                    $position++;
                    $undeletedIds[] = $id;
                }   
            }

            $this->deleteById($product->id, $undeletedIds);
        });
        
        return new SuccessResource();
    }

    ////////////////////
    // private methods//
    ////////////////////

    private function deleteById($productId, array $undeletedIds)
    {
        $images = ProductImage::where('product_id', $productId)->get();

        foreach ($images as $image ) {
            if (in_array($image->id, $undeletedIds) == false) {
                $imageService = new ImageService($image);
                $imageService->delete();
                $image->delete();
            }
        }
    }
}
