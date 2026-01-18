<?php

namespace Ffhs\FfhsTasks\Database\Factories;

use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['type']),

            'settings' => null,
            'data' => null,

            'can_cancel' => $this->faker->boolean(),
            'cancelled' => false,
            'finished' => false,

            'deadline_at' => $this->faker->dateTimeBetween(
                startDate: Carbon::tomorrow(),
                endDate: Carbon::today()->addDays(30)
            ),

            'start_at' => $this->faker->dateTimeBetween(
                startDate: Carbon::today()->subDays(30),
                endDate: Carbon::yesterday(),
            ),
        ];
    }

    public function cancelled(): self
    {
        return $this->state([
            'can_cancel' => true,
            'cancelled' => true,
        ]);
    }

    public function finished(): self
    {
        return $this->state([
            'finished' => true,
        ]);
    }
}
