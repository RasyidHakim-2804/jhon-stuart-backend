<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductImage extends Model implements CanUploadImage
{
    use HasFactory, HasImage;

    protected $table            = 'product_images';
    protected $imageUrlColumn   = 'path';
    protected $format           = '.webp';
    protected $disk             = 'public';
    protected $dirPath          = 'upload/images/product/';

    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    //Accessor path
    public function path(): Attribute
    {
        return new Attribute(
            get: function ($value = null) {
                if ($value == null) {
                    return null;
                }
                return  config('app.frontend_url') . 'storage/' . $value;
            }
        );
    }

    public function scopeSaved(Builder $query)
    {
        $query->where('position', '!=', null);
    }
}
