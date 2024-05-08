<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Service\Abstracts\Model\MyBaseModel;
use App\Models\Product;

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
}
