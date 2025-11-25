<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProjectFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
