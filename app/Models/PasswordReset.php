<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasswordReset
{
    use HasFactory;

    protected $table = 'password_resets';

    protected $fillable = [
        'email',
        'token'
    ];
}
