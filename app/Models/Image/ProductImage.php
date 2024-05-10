<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Service\Abstracts\Model\MyBaseModel;
use App\Models\Product;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ProductImage extends MyBaseModel
{
    use HasFactory;

    protected $table      = 'product_images';
    protected $pathColumn = 'path';
    protected $format     = '.webp';
    protected $disk       = 'public';
    protected $dirPath    = 'upload/images/product/';

    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    //Accessor path
    public function path(): Attribute
    {
        return new Attribute(
            get: fn (string $value) => config('app.frontend_url') . 'storage/' . $value
        );
    }
}
