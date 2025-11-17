<?php

namespace App;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskStatusTransition;
use Illuminate\Support\Facades\Log;

class TaskStateMachine
{
    private static ?TaskStateMachine $instance = null;

    private array $transitionMap = [];

    private function __construct()
    {
        $this->loadTransitions();
    }


    public static function getInstance(): TaskStateMachine
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function loadTransitions(): void
    {
        $this->transitionMap = TaskStatusTransition::all()
            ->groupBy('from_status_id')
            ->map(fn($items) => $items->pluck('to_status_id')->toArray())
            ->toArray();
    }

    public function getAllowedTransitions(int $statusId)
    {
        $allowedIds = $this->transitionMap[$statusId] ?? [];

        $allowedIds = collect($allowedIds)->flatten()->filter(fn($id) => is_numeric($id))->values()->all();

        if (empty($allowedIds)) {
            return collect();
        }

        return TaskStatus::whereIn('id', $allowedIds)->get();
    }


    public function canTransition(Task $task, int $targetStatusId): bool
    {
        $fromId = $task->status_id;
        return in_array($targetStatusId, $this->transitionMap[$fromId] ?? []);
    }

    public function transition(Task $task, int $targetStatusId): bool
    {
        if (!$this->canTransition($task, $targetStatusId)) {
            Log::warning("Nepovolený prechod z #{$task->status_id} na #{$targetStatusId} (task #{$task->id})");
            return false;
        }

        $task->status_id = $targetStatusId;
        $task->save();

        Log::info("Úloha #{$task->id} zmenila stav na ID {$targetStatusId}");
        return true;
    }

    public function refresh(): void
    {
        $this->loadTransitions();
    }
}
