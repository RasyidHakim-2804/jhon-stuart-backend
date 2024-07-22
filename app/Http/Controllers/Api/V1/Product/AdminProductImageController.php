<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\Image\DeleteImagerequest;
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
        $images = $product->images()->saved()->get();

        return ProductImageResource::collection($images);
    }

    public function store(Product $product,StoreImageRequest $request)
    {
        try {
            DB::beginTransaction();

            $image         = new ProductImage();
            $imageService  = new ImageService($image);          

            $image->product_id  = $product->id;
            $image->path        = $imageService->upload($request->images)->path;
            $image->position    = null;
            $image->save();

            DB::commit();

            return new ProductImageResource($image);

        } catch (\Throwable $th) {
            
            DB::rollBack();

            if (isset($imageService)) {
                $imageService->rollback();
            }

            throw $th;
        }
    }

    public function saveImages(SaveImageRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            $imagesId       = $request->validated('images_id');
            $savedImages    = $product->images()->whereIn('id', $imagesId)->get();
            $unsavedImaged  = $product->images()->whereNotIn('id', $imagesId)->get();                                                                   
            $imageServices  = ImageService::collection($unsavedImaged);

            $imageServices->delete();
            $product->images()->whereNotIn('id', $imagesId)->delete(); 
            
            foreach ($imagesId as $position => $id) {
                $image           = $savedImages->where('id', '=', $id)->firstOrFail();
                $image->position = $position + 1;
                $image->save();
            }

            DB::commit();            

            return new SuccessResource();

        } catch (\Throwable $th) {
            
            DB::rollBack();

            if (isset($imageServices)) {
                $imageServices->rollback();
            }
            
            throw $th;
        }
            
    }
}
