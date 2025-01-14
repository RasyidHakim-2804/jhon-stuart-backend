<?php

namespace App\Http\Controllers\Api\V1\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\DeleteCartRequest;
use App\Http\Requests\Api\Cart\StoreCartRequest;
use App\Http\Resources\Cart\CartResource;
use App\Http\Resources\Product\ProductVariantResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserCartController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return CartResource::collection(
            Cart::with([
                'product.variants',
                'variant'
            ])->where('user_id', '=', $user->id)->get()
        );
    }

    public function listSide()
    {
        return CartResource::collection(Cart::with([
            'product',
            'variant',
        ])->where('user_id', '=', auth()->user()->id)->get());
    }

    public function store(StoreCartRequest $request)
    {
        DB::beginTransaction();
        try {

            $cart    = Cart::where('product_variant_id', '=', $request->product_variant_id)->firstOrNew();

            $cart->user_id            = auth()->user()->id;
            $cart->product_active     = Product::where('id', $request->product_id)->value('is_active');
            $cart->variant_active     = ProductVariant::where('id', $request->product_variant_id)->value('is_active');
            $cart->product_id         = $request->product_id;
            $cart->product_variant_id = $request->product_variant_id;
            $cart->quantity           = $cart->quantity + 1;
            $cart->save();

            DB::commit();

            return $this->successStore();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(DeleteCartRequest $request)
    {
        DB::transaction(function() use ($request) {

            /** @var \App\Models\User */
            $user = auth()->user();
            $user->variantCart()->detach([
                $request->validated('product_variant_id')
            ]);
        });

        return $this->successResponse();
    }
}
