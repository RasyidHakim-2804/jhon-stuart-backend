<?php

namespace App\Http\Controllers\Api\V1\Wishlist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wishlist\CreateWishlistRequest;
use App\Http\Requests\Api\Wishlist\DeleteWishlistRequest;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductVariantResource;
use App\Http\Resources\Wishlist\WishlistResource;
use App\Models\Wishlist;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserWishlistController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return WishlistResource::collection(
            Wishlist::with([
                'product.variants',
                'variant'
            ])->where('user_id', '=', $user->id)->get()
        );
    }

    public function listSide(Request $request)
    {
        /** @var \App\Models\ProductVariant */
        $productVariant = $request->user()->variantWishlists()->with([
            'product.thumbnailImage'
        ])->paginate(5);

        return ProductVariantResource::collection($productVariant);
    }

    public function store(CreateWishlistRequest $request)
    {
        DB::transaction(function() use ($request) {

            /** @var \App\Models\User */
            $user = auth()->user();
            $user->variantWishlists()->syncWithoutDetaching([
                $request->validated('product_variant_id') => [
                    'created_at' => now(),
                    'product_id' => $request->product_id,
                ]
            ]);
        });

        return $this->successStore();
    }

    public function destroy(DeleteWishlistRequest $request)
    {
        DB::transaction(function() use ($request) {

            /** @var \App\Models\User */
            $user = auth()->user();
            $user->variantWishlists()->detach([
                $request->validated('product_variant_id')
            ]);
        });

        return $this->successResponse();
    }
}
