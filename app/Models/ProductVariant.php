<?php

namespace App\Models;

use App\Services\Cart\CartService;
use App\Services\Logs\AdminLogService;
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
        return $this->belongsToMany(User::class, 'wishlists', 'product_variant_id', 'user_id');
    }

    public function cartBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'carts', 'product_variant_id', 'user_id');
    }

    // === SCOPE ===

    public function scopeDefault(Builder $query, bool $default = true)
    {
        return $query->where('is_default', '=', $default);
    }

    public function scopeActive(Builder $query, bool $active = true)
    {
        return $query->where('is_active', '=', $active);
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

    // === BOOT ===

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;

        static::created(function($data) use($logService) {
            $logService->store('create', 'product_variant', $data->id);
        });

        static::updated(function($data) use($logService) {
            if ($logService) $logService->store('update', 'product_variant', $data->id);
            (new CartService)->updateVariant($data);
        });

        static::deleted(function($data) use($logService) {
            if ($logService) $logService->store('delete', 'product_variant', $data->id);
            (new CartService)->deleteVariant($data);
        });
    }
}
