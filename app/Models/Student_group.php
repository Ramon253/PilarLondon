<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student_group extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'group_id'
    ];

    public $incrementing = false;
}
