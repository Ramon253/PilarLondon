<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Join_code extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';

    protected $fillable = [
        'user_id',
        'role',
        'code'
    ];
}
