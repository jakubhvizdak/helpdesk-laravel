<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTime extends Model
{
    use HasFactory;

    protected $table = 'task_times';

    protected $fillable = ['task_id', 'user_id', 'hours', 'worked_at', 'activity_id', 'description', 'service_hour_id'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceHour()
    {
        return $this->belongsTo(ServiceHour::class);
    }

    public function activity()
    {
        return $this->belongsTo(TaskActivity::class, 'activity_id');
    }

}

