<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wait_list extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'phone_number',
        'places'
    ];
}
