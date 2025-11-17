<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status_id',
        'due_date',
        'end_date',
        'project_id',
        'assigned_to',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'date',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function taskTimes()
    {
        return $this->hasMany(TaskTime::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->with('user');
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    public function getStatusNameAttribute()
    {
        return $this->status?->name;
    }

    public function getStatusLabelAttribute()
    {
        return $this->status?->label ?? ucfirst($this->status?->name);
    }
}
