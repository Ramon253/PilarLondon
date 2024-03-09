<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    public function getGroups()
    {
        return self::query()
            ->select('groups.*')
            ->join('student_groups', 'students.id', '=', 'student_groups.student_id')
            ->join('groups', 'student_groups.group_id', '=', 'groups.id')
            ->where('students.id', $this->id)
            ->get();
    }

    static function isStudent($user_id)
    {
        return self::query()->where('user_id', $user_id)->first();
    }
}
