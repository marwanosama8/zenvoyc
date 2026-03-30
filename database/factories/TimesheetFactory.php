<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Timesheet>
 */
class TimesheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake('de_DE')->dateTimeBetween('-6 months', 'now'),
            'start_time' => function () {
                $start = Carbon::now()->subDays(rand(0, 30))->setTime(rand(0, 23), rand(0, 59));
                return $start->toTimeString();
            },
            'end_time' => function (array $attributes) {
                $start = Carbon::parse($attributes['start_time']);
                $end = $start->copy()->addHours(rand(3, 8));
                return $end->toTimeString();
            },
            'manual_time' => null,
            'is_active' => 0,
            'description' => $this->faker->sentence(),

        ];

    }
}
