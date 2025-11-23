<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskEditLog;

class TaskEdited extends Notification
{
    use Queueable;

    public $task;
    public $log;
    public $user;

    public function __construct($task, $log, $user)
    {
        $this->task = $task;
        $this->log = $log;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $actionText = $this->log->type === TaskEditLog::TYPE_TASK_CREATED
            ? 'vytvoril úlohu'
            : 'upravil úlohu';

        return [
            'title' => "{$this->task->id} - {$this->task->title}",
            'message' => "{$this->user->name} {$this->user->surname} {$actionText} {$this->task->title}",
            'link' => "/task/{$this->task->id}"
        ];
    }
}
