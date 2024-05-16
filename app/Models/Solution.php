<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\returnSelf;

class Solution extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'note',
        'student_id',
        'assignment_id'
    ];


}
