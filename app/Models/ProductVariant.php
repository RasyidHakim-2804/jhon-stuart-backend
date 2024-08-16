<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([AdminActionObserver::class])]
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

    public const VARIANT_TYPE = [
        'S', 'L', 'M', 'set', 'jumbo'
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
