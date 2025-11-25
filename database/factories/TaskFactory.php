<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskStatus;

class TaskFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'project_id' => Project::factory(),
            'status_id' => TaskStatus::factory(),
            'created_by' => User::factory()->create()->id,
            'assigned_to' => null,
            'private' => false,
            'due_date' => null,
        ];
    }
}
