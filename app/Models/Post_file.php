<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post_file extends Model
{
    use HasFactory;

    protected $fillable = [
      'post_id',
      'file_name',
      'file_path',
      'mimetype',
      'multimadia'
    ];
}
