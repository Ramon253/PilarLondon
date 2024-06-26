<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post_comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'public',
        'user_id',
        'parent_id',
        'post_id'
    ];
}
