<?php

namespace App\Http\Controllers\Api\V1\Wishlist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Wishlist\CreateWishlistRequest;
use App\Http\Requests\Api\V1\Wishlist\DeleteWishlistRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Http\Resources\Api\V1\Product\ProductVariantResource;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserWishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User */
        $user = $request->user()->load('wishlists.product');
        
        return ProductVariantResource::collection($user->wishlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateWishlistRequest $request)
    {
        DB::transaction(function() use ($request) {
            
            /** @var \App\Models\User */
            $user = $request->user();
            $user->wishlists()->syncWithoutDetaching([
                $request->validated('product_variant_id') => ['created_at' => now()]
            ]);
        });

        return new SuccessResource(JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteWishlistRequest $request)
    {
        DB::transaction(function() use ($request) {
            
            /** @var \App\Models\User */
            $user = $request->user();
            $user->wishlists()->detach([
                $request->validated('product_variant_id')
            ]);
        });

        return new SuccessResource(JsonResponse::HTTP_CREATED);
    }
}
