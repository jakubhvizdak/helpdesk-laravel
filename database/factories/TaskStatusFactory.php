<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskStatusFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->unique()->randomElement([
                'nove', 'priradena', 'dokoncena'
            ]),
            'label' => fake()->word(),
            'color_bg' => '#cccccc',
            'color_text' => '#333333',
        ];
    }
}
