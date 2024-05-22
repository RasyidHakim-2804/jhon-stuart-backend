<?php

namespace App\Models\Image;

use App\Models\Article;
use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ArticleImage extends Model implements CanUploadImage
{
    use HasFactory, HasImage;

    protected $table      = 'article_images';
    protected $pathColumn = 'path';
    protected $format     = '.webp';
    protected $disk       = 'public';
    protected $dirPath    = 'upload/images/article/';

    public function article() : BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    //Accessor path
    public function path(): Attribute
    {
        return new Attribute(
            get: fn (string $value) => config('app.frontend_url') . 'storage/' . $value
        );
    }
}
