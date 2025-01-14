<?php

namespace App\Services\Wishlist;

use App\Models\Wishlist;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class WishlistService
{
    public function updateProduct(Product $product)
    {
        DB::beginTransaction();
        try {

            Wishlist::where('product_id', '=', $product->id)->update([
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

            Wishlist::where('product_id', '=', $product->id)->update([
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

            Wishlist::where('product_variant_id', '=', $variant->id)->update([
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

            Wishlist::where('product_variant_id', '=', $variant->id)->update([
                'deleted_at' => now(),
            ]);

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
