<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TaskEditLog;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;

class TaskEditLogController extends Controller
{
    public function index($taskId)
    {
        $logs = TaskEditLog::with('user')
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                if ($log->type === 'status_change') {
                    $log->old_value = $log->old_value ? TaskStatus::find($log->old_value)?->label : '-';
                    $log->new_value = $log->new_value ? TaskStatus::find($log->new_value)?->label : '-';
                }

                if ($log->type === 'assigned_user') {
                    $log->old_value = $log->old_value ? User::find($log->old_value)?->name . ' ' . User::find($log->old_value)?->surname : 'Nepriradené';
                    $log->new_value = $log->new_value ? User::find($log->new_value)?->name . ' ' . User::find($log->new_value)?->surname : 'Nepriradené';
                }

                return $log;
            });

        return response()->json($logs);
    }
}
