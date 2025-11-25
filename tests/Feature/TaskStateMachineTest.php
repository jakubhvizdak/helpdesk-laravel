<?php

namespace Tests\Unit;

use App\TaskStateMachine;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskStatusTransition;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskStateMachineTest extends TestCase
{
    use RefreshDatabase;

    public function test_singleton_returns_same_instance()
    {
        $a = TaskStateMachine::getInstance();
        $b = TaskStateMachine::getInstance();

        $this->assertSame($a, $b);
    }

    public function test_get_allowed_transitions_returns_correct_statuses()
    {
        $from = TaskStatus::factory()->create();
        $to = TaskStatus::factory()->create();
        TaskStatusTransition::factory()->create([
            'from_status_id' => $from->id,
            'to_status_id' => $to->id,
        ]);

        $machine = TaskStateMachine::getInstance();
        $machine->refresh();
        $allowed = $machine->getAllowedTransitions($from->id);

        $this->assertCount(1, $allowed);
        $this->assertEquals($to->id, $allowed->first()->id);
    }

    public function test_can_transition_returns_true_or_false()
    {
        $from = TaskStatus::factory()->create();
        $to = TaskStatus::factory()->create();
        TaskStatusTransition::factory()->create([
            'from_status_id' => $from->id,
            'to_status_id' => $to->id,
        ]);

        $task = Task::factory()->create(['status_id' => $from->id]);

        $machine = TaskStateMachine::getInstance();

        $this->assertTrue($machine->canTransition($task, $to->id));
        $this->assertFalse($machine->canTransition($task, 999));
    }

    public function test_transition_changes_status_and_logs()
    {
        Log::shouldReceive('info')->once();
        Log::shouldReceive('warning')->never();

        $from = TaskStatus::factory()->create();
        $to = TaskStatus::factory()->create();
        TaskStatusTransition::factory()->create([
            'from_status_id' => $from->id,
            'to_status_id' => $to->id,
        ]);

        $task = Task::factory()->create(['status_id' => $from->id]);

        $machine = TaskStateMachine::getInstance();
        $result = $machine->transition($task, $to->id);

        $this->assertTrue($result);
        $this->assertEquals($to->id, $task->fresh()->status_id);
    }

    public function test_transition_forbidden_logs_warning()
    {
        Log::shouldReceive('info')->never();
        Log::shouldReceive('warning')->once();

        $from = TaskStatus::factory()->create();
        $task = Task::factory()->create(['status_id' => $from->id]);

        $machine = TaskStateMachine::getInstance();
        $result = $machine->transition($task, 999);

        $this->assertFalse($result);
        $this->assertEquals($from->id, $task->fresh()->status_id);
    }
}
