<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Laravel\Prompts\select;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dead_line',
        'description',
        'group_id',
        'inClass'
    ];

    public function getComments()
    {
        return self::query()
            ->select('assignment_comments.*', 'users.name as user_name')
            ->join('users', 'Assignment_comments.user_id', '=', 'users.id')
            ->from('assignment_comments')
            ->where('assignment_comments.assignment_id', $this->id)
            ->get();
    }


}
