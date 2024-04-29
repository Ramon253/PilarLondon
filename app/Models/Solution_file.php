<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solution_file extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'solution_id',
        'file_name',
        'file_path',
        'mimetype',
        'multimadia'
      ];
}
