<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
    ];

    public function taskTimes()
    {
        return $this->hasMany(TaskTime::class);
    }
}
