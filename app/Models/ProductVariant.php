<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 
        'product_id', 
        'code', 
        'stock', 
        'available', 
        'idr_price', 
        'usd_price',
        'stock'
    ];

    protected $table = 'product_variants';

    public const VARIANT_TYPE = [
        's', 'l', 'm', 'set', 'jumbo'
    ];

    public const DEFAULT_TYPE = 'default';

    public function scopeDefault(Builder $query, bool $default = true)
    {
        if ($default) {
            $query->where('type', '=', self::DEFAULT_TYPE);
        } else {
            $query->where('type', '!=', self::DEFAULT_TYPE);
        }
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function wishlistBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_wishlists', 'product_variant_id', 'user_id');
    }
}
