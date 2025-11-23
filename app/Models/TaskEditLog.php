<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskEditLog extends Model
{
    use HasFactory;

    protected $table = 'task_edit_log';

    protected $fillable = [
        'task_id',
        'user_id',
        'type',
        'old_value',
        'new_value',
        'notified',
    ];

    public const TYPE_STATUS_CHANGE   = 'status_change';
    public const TYPE_COMMENT_ADDED   = 'comment_added';
    public const TYPE_ASSIGNED_USER   = 'assigned_user';
    public const TYPE_DUE_DATE        = 'due_date';
    public const TYPE_TASK_CREATED        = 'task_created';

    public static function getTypes(): array
    {
        return [
            self::TYPE_STATUS_CHANGE,
            self::TYPE_COMMENT_ADDED,
            self::TYPE_ASSIGNED_USER,
            self::TYPE_DUE_DATE,
            self::TYPE_TASK_CREATED,
        ];
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isComment(): bool
    {
        return $this->type === self::TYPE_COMMENT_ADDED;
    }

    public function isStatusChange(): bool
    {
        return $this->type === self::TYPE_STATUS_CHANGE;
    }
}
