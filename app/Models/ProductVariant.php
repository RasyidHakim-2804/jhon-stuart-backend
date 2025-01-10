<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    public const NAME = [
        's', 'l', 'm', 'set', 'jumbo'
    ];

    // === RELATIONS ===

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function wishlistBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_wishlists', 'product_variant_id', 'user_id');
    }

    // === SCOPE ===

    public function scopeDefault(Builder $query, bool $default = true)
    {
        return $query->where('is_default', '=', $default);
    }

    public function scopeInWishlist(Builder $query, $user_id = 0)
    {
        $query->addSelect('*',DB::raw(
            "(select user_id from wishlists " .
            "where product_variant_id = product_variants.id " .
            "and user_id = " . intval($user_id) . " " .
            "limit 1) as in_wishlist"
        ));
    }
}
