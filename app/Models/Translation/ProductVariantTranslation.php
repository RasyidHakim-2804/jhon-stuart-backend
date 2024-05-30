<?php

namespace App\Models\Translation;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantTranslation extends Model
{
    use HasFactory;

    public static $language = [ 'id', 'en'];

    public $fillable = [
        'description', 'lang'
    ];

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
