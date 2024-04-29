<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment_file extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'file_name',
        'assignment_id',
        'mime_type',
        'multimadia'
    ];

}
