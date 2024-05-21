<?php

namespace App\Models\Translation;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTranslation extends Model
{
    use HasFactory;

    public $fillable = [
        'description', 'full_description'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
