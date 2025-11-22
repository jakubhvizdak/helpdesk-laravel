<?php

namespace App\Models;

/**
 * @property string|null $content
 */

use Illuminate\Database\Eloquent\Model;

class ProjectDocumentationSection extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'content',
        'updated_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
