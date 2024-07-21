<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake('de_DE')->sentence(),
            'description' => fake('de_DE')->sentence(10),
            'tags' => ['tag' . rand(111, 999)],
            'priority' => rand(1, 3),
            'done' => fake()->boolean(30),
            'subtasks' => [
                'title' => fake('de_DE')->sentence(4),
                'done_subtask' => fake()->boolean(30),
            ]
        ];
    }
}
