<?php

namespace Database\Factories;

use App\Models\TaskStatusTransition;
use App\Models\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskStatusTransitionFactory extends Factory
{
    protected $model = TaskStatusTransition::class;

    public function definition()
    {
        return [
            'from_status_id' => TaskStatus::factory(),
            'to_status_id' => TaskStatus::factory(),
        ];
    }
}
