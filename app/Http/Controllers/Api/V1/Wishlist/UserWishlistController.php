<?php

namespace App\Http\Controllers\Api\V1\Wishlist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wishlist\CreateWishlistRequest;
use App\Http\Requests\Api\Wishlist\DeleteWishlistRequest;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductVariantResource;
use App\Http\Resources\Wishlist\WishlistResource;
use App\Models\Product;
use App\Models\ProductVariant;
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
                'product' => fn($query) => $query->withTrashed(),
                'variant' => fn($query) => $query->withTrashed(),
                'product.variants',
            ])->where('user_id', '=', $user->id)->get()
        );
    }

    public function listSide(Request $request)
    {
        return WishlistResource::collection(Wishlist::with([
            'product' => fn($query) => $query->withTrashed(),
            'variant' => fn($query) => $query->withTrashed(),
        ])->where('user_id', '=', auth()->user()->id)->get());
    }

    public function store(CreateWishlistRequest $request)
    {
        DB::beginTransaction();
        try {

            $wishlist    = Wishlist::where('product_variant_id', '=', $request->product_variant_id)->firstOrNew();

            $wishlist->user_id            = auth()->user()->id;
            $wishlist->product_active     = Product::where('id', $request->product_id)->value('is_active');
            $wishlist->variant_active     = ProductVariant::where('id', $request->product_variant_id)->value('is_active');
            $wishlist->product_id         = $request->product_id;
            $wishlist->product_variant_id = $request->product_variant_id;
            $wishlist->save();

            DB::commit();

            return $this->successStore();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
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
