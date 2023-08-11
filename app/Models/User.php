<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id');
    }

    public function taskMembers()
    {
        return $this->hasMany(TaskMember::class, 'user_id');
    }

    public function taskTags()
    {
        return $this->hasMany(TaskTag::class, 'user_id');
    }
}
