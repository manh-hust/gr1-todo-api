<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskMember extends Model
{
    use HasFactory;

    protected $table = "task_members";

    protected $fillable = [
        'task_id',
        'user_id',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function member()
    {
        return $this->belongsTo(User::class);
    }

    public function getMemberNameAttribute()
    {
        return $this->member->name;
    }

    public function getMemberEmailAttribute()
    {
        return $this->member->email;
    }
}
