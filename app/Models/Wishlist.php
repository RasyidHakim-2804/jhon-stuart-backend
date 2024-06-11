<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * this class is model for pivot table between users and product 
 */
class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'user_wishlists';
}
