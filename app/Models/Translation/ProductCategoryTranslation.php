<?php

namespace App\Models\Translation;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCategoryTranslation extends Model
{
    use HasFactory;

    public function ProductCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
