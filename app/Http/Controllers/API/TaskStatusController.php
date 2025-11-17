<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskStatus;
use App\TaskStateMachine;
use Illuminate\Http\Request;

class TaskStatusController extends Controller
{
    public function index()
    {
        $statuses = TaskStatus::select('id', 'name', 'label', 'color_bg', 'color_text')->get();
        return response()->json($statuses);
    }

    public function updateStatus(Request $request, Task $task)
    {
        $newStatusId = $request->input('status_id');

        $newStatus = TaskStatus::find($newStatusId);
        if (!$newStatus) {
            return response()->json(['error' => 'Neznámy status.'], 422);
        }

        $oldStatusId = $task->status_id;

        $stateMachine = TaskStateMachine::getInstance();

        if (!$stateMachine->canTransition($task, $newStatusId)) {
            $from = $task->status?->label ?? 'Neznámy';
            $to = $newStatus->label;
            return response()->json([
                'error' => "Prechod zo stavu '{$from}' na '{$to}' nie je povolený."
            ], 422);
        }

        $task->status_id = $newStatusId;

        if (in_array($newStatusId, [3, 4])) {
            if (!$task->end_date) {
                $task->end_date = now();
            }
        } else {
            $task->end_date = null;
        }

        $task->save();

        $task->load('status');

        return response()->json($task);
    }

    public function allowedTransitions(Task $task)
    {
        $stateMachine = TaskStateMachine::getInstance();

        $transitions = $stateMachine->getAllowedTransitions($task->status_id);

        $statuses = $transitions->map(fn($status) => [
            'id' => $status->id,
            'label' => $status->label,
        ]);

        return response()->json($statuses);
    }
}
