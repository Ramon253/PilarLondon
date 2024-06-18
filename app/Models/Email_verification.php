<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email_verification extends Model
{
    use HasFactory;

    protected $table = 'email_verification';

    protected $fillable = [
        'token',
        'user_id'
    ];
}
