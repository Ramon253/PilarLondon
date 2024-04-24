<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solution_link extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'link_name',
        'solution_id'
    ];
}
