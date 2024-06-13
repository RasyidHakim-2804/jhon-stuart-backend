<?php

namespace App\Models\Translation;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'lang',
        'body'
    ];

    public function article() : BelongsTo 
    {
        return $this->belongsTo(Article::class);    
    }
}
