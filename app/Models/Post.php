<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'description',
        'group_id'
    ];

    public function getComments()
    {
        return self::query()
            ->select('post_comments.*', 'users.name as user_name')
            ->join('users', 'post_comments.user_id', '=', 'users.id')
            ->from('post_comments')
            ->where('post_comments.post_id', $this->id)
            ->get();
    }
}
