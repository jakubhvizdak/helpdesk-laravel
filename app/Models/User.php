<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'surname', 'email', 'password', 'role', 'last_login_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_users', 'user_id', 'project_id');
    }


    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function taskTimes()
    {
        return $this->hasMany(TaskTime::class);
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->name} {$this->surname}");
    }
}
