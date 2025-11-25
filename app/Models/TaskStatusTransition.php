<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskStatusTransition extends Model
{
    use hasFactory;

    protected $fillable = [
        'from_status_id',
        'to_status_id',
    ];

    public function fromStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'from_status_id');
    }

    public function toStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'to_status_id');
    }
}
