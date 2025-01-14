<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function updateProduct(Product $product)
    {
        DB::beginTransaction();
        try {

            Cart::where('product_id', '=', $product->id)->update([
                'product_active' => $product->is_active,
            ]);

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function deleteProduct(Product $product)
    {
        DB::beginTransaction();
        try {

            Cart::where('product_id', '=', $product->id)->update([
                'deleted_at' => now(),
            ]);

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function updateVariant(ProductVariant $variant)
    {
        DB::beginTransaction();
        try {

            Cart::where('product_variant_id', '=', $variant->id)->update([
                'variant_active' => $variant->is_active,
            ]);

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function deleteVariant(ProductVariant $variant)
    {
        DB::beginTransaction();
        try {

            Cart::where('product_variant_id', '=', $variant->id)->update([
                'deleted_at' => now(),
            ]);

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
