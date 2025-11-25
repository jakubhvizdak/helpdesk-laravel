<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskEditLog;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    /** @test */
    public function it_returns_only_tasks_from_projects_where_user_is_member()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $project->users()->attach($user->id);

        $taskInProject = Task::factory()->create(['project_id' => $project->id]);
        $taskOther = Task::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $taskInProject->id
        ]);
        $response->assertJsonMissing([
            'id' => $taskOther->id
        ]);
    }

    /** @test */
    public function user_cannot_view_task_outside_his_projects()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_creates_a_new_task_and_logs_the_creation()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $status = TaskStatus::factory()->create(['name' => 'priradena']);

        $project->users()->attach($user->id);

        $payload = [
            'title' => 'Test task',
            'description' => 'Popis',
            'project_id' => $project->id,
            'assigned_to' => null,
            'private' => false,
        ];

        $response = $this->actingAs($user)->postJson('/api/tasks', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test task',
            'project_id' => $project->id,
        ]);

        $task = Task::first();

        $this->assertDatabaseHas('task_edit_log', [
            'task_id' => $task->id,
            'type' => TaskEditLog::TYPE_TASK_CREATED
        ]);
    }
}
