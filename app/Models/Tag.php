<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = "tags";

    protected $fillable = [
        'name',
        'tag_name',
        'color',
    ];

    public function tasks()
    {
        return $this->hasMany(TaskTag::class);
    }
}
