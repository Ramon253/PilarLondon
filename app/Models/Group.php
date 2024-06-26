<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'level',
        'lesson_days',
        'lessons_time',
        'banner',
        'capacity'
    ];

    public function getStudents()  {
        return self::query()
            ->select('students.*')
            ->join('student_groups', 'groups.id', '=', 'student_groups.group_id')
            ->join('students', 'student_groups.student_id', '=', 'students.id')
            ->where('groups.id', $this->id)
            ->get();
    }
}
