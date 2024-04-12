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
        'description'
    ];

    public function getComments()
    {
        return self::query()
            ->select('post_comments.*', 'users.name')
            ->join('users', 'Post_comments.user_id', '=', 'users.id')
            ->from('post_comments')
            ->where('Post_comments.post_id', $this->id)
            ->get();
    }
}
