<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'label', 'color_bg', 'color_text'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
