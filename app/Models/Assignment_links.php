<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment_links extends Model
{
    use HasFactory;
    protected $fillable = [
        'link',
        'link_name'
    ];
}
