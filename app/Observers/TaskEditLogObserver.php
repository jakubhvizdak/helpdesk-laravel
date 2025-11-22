<?php

namespace App\Observers;

use App\Models\TaskEditLog;
use App\Notifications\TaskEdited;
use Illuminate\Support\Facades\Notification;

class TaskEditLogObserver
{
    public function created(TaskEditLog $log)
    {
        $task = $log->task()->with('project.users')->first();
        $editor = $log->user;

        $usersToNotify = $task->project->users->filter(function($u) use ($editor, $task) {
            if ($u->id === $editor->id) return false;

            if ($task->private && $u->role === 'customer') return false;

            return true;
        });

        Notification::send($usersToNotify, new TaskEdited($task, $log, $editor));

        $log->notified = 1;
        $log->saveQuietly();
    }
}
