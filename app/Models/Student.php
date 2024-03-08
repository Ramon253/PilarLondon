<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    static function isStudent($user_id)
    {
        return self::query()->where('user_id', $user_id)->first();
    }
}
