<?php

namespace App\Http\Controllers\Api\V1\Product\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductImage\SaveProductImageRequest;
use App\Http\Requests\Api\ProductImage\StoreProductImageRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use Illuminate\Support\Facades\DB;

class AdminProductImageController extends Controller
{
    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('product_image');
    }

    public function index(Product $product)
    {
        $images = $product->images()->saved()->get();

        return ProductImageResource::collection($images);
    }

    public function store(Product $product, StoreProductImageRequest $request)
    {
        try {
            DB::beginTransaction();

            $productImage = new ProductImage();
            $imageService = new ImageService($productImage->imagePath(), $request->image);
            $imageService->save();          

            $productImage->product_id  = $product->id;
            $productImage->image       = $imageService->getImageName();
            $productImage->position    = null;
            $productImage->save();

            DB::commit();

            return new SuccessResource(
                data: new ProductImageResource($productImage)
            );

        } catch (\Throwable $th) {
            
            DB::rollBack();

            throw $th;
        }
    }

    public function saveImages(SaveProductImageRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            $imagesId       = $request->validated('images_id');
            $savedImages    = $product->images()->whereIn('id', $imagesId)->get();
            $unsavedImages  = $product->images()->whereNotIn('id', $imagesId)->get();                                                                   

            $product->images()->whereNotIn('id', $imagesId)->delete(); 
            
            foreach ($imagesId as $position => $id) {
                $image           = $savedImages->where('id', '=', $id)->firstOrFail();
                $image->position = $position + 1;
                $image->save();
            }

            foreach ($unsavedImages as $unsavedImage) {
                $imageService = new ImageService($unsavedImage->imagePath());
                $imageService->delete($unsavedImage->image);
            }

            DB::commit();            

            return new SuccessResource();

        } catch (\Throwable $th) {
            
            DB::rollBack();
            
            throw $th;
        }
            
    }
}
