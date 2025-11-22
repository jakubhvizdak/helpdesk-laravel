<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users', 'project_id', 'user_id')
            ->using(ProjectUser::class)
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function service_hours()
    {
        return $this->hasOne(ServiceHour::class, 'project_id');
    }

    protected static function booted()
    {
        static::deleting(function ($project) {
            $project->service_hours()->delete();
        });
    }

    public function documentationSections()
    {
        return $this->hasMany(ProjectDocumentationSection::class);
    }

}
