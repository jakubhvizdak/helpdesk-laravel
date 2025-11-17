<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ServiceHour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['project_id', 'hours_total', 'hours_remaining', 'hourly_price'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function taskTimes()
    {
        return $this->hasMany(TaskTime::class);
    }
}
