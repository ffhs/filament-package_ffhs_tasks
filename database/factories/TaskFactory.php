<?php

namespace Ffhs\FfhsTasks\Database\Factories;

use Ffhs\FfhsTasks\Enums\TaskStatus;
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
            'status' => TaskStatus::InProgress,

            'settings' => null,
            'data' => null,

            'can_be_cancelled' => $this->faker->boolean(),
            'cancelled_at' => null,
            'completed_at' => null,

            'deadline_at' => $this->faker->dateTimeBetween(
                startDate: Carbon::tomorrow(),
                endDate: Carbon::today()->addDays(30)
            ),

            'starts_at' => $this->faker->dateTimeBetween(
                startDate: Carbon::today()->subDays(30),
                endDate: Carbon::yesterday(),
            ),
        ];
    }

    public function cancelled(): self
    {
        return $this->state([
            'can_be_cancelled' => true,
            'cancelled_at' => $this->faker->dateTimeBetween(
                startDate: Carbon::today()->subDays(30),
                endDate: Carbon::yesterday(),
            ),
            'status' => TaskStatus::Cancelled,
        ]);
    }

    public function completed(): self
    {
        return $this->state([
            'completed_at' => $this->faker->dateTimeBetween(
                startDate: Carbon::today()->subDays(30),
                endDate: Carbon::yesterday(),
            ),
            'status' => TaskStatus::Completed,
        ]);
    }
}
