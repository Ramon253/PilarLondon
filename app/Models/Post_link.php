<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post_link extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'link_name',
        'post_id'
    ];
}
