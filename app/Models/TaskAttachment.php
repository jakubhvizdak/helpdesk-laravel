<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TaskAttachment extends Model
{
    protected $fillable = [
        'task_comment_id',
        'filename',
        'path',
        'type',
        'size'
    ];

    public function comment()
    {
        return $this->belongsTo(TaskComment::class, 'task_comment_id');
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . preg_replace('#^public/#', '', $this->path));
    }

}


