<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

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
}
