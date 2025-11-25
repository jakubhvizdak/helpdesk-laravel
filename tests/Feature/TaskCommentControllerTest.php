<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskEditLog;

class TaskCommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    /** @test */
    public function it_returns_comments_for_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $comments = TaskComment::factory()->count(3)->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/tasks/{$task->id}/comments");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function user_can_add_comment_to_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $payload = [
            'text' => 'Test comment'
        ];

        $response = $this->actingAs($user)
            ->postJson("/api/tasks/{$task->id}/comments", $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'text' => 'Test comment',
        ]);
    }

    /** @test */
    public function creating_comment_creates_task_edit_log_entry()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $this->actingAs($user)
            ->postJson("/api/tasks/{$task->id}/comments", [
                'text' => 'Comment log test'
            ])->assertStatus(201);

        $this->assertDatabaseHas('task_edit_log', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'type' => TaskEditLog::TYPE_COMMENT_ADDED,
            'new_value' => 'Comment log test',
        ]);
    }

    /** @test */
    public function comment_requires_text()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/tasks/{$task->id}/comments", [
                'text' => ''
            ]);

        $response->assertStatus(422);
    }
}
