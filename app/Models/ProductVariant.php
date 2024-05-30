<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Translation\ProductVariantTranslation;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'name', 'price','stock', 'symbol'
    ];

    protected $table = 'product_variants';

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductVariantTranslation::class);
    }
}
