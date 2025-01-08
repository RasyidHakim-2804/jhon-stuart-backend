<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductImage extends Model
{
    use HasFactory, ImageStorageTrait;

    protected $imageSetting = [
        'folder' => 'product',
        'field'  => [
            'image',
        ],
    ];

    // === RELATIONS ===

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeSaved(Builder $query)
    {
        $query->where('position', '!=', null);
    }
}
