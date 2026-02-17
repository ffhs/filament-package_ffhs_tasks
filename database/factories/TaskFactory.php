<?php

namespace Ffhs\FfhsTasks\Database\Factories;

use Ffhs\FfhsTasks\Enums\TaskPrivacy;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;
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
            'type' => $this->faker->randomElement(
                array_keys(TaskType::getAllTypes())
            ),
            'status' => TaskStatus::InProgress,
            'privacy' => TaskPrivacy::Public,

            'extra' => null,
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

    public function private(): self
    {
        return $this->state([
            'privacy' => TaskPrivacy::Private,
        ]);
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

    public function expired(): self
    {
        return $this->state([
            'cancelled_at' => $this->faker->dateTimeBetween(
                startDate: Carbon::today()->subDays(30),
                endDate: Carbon::yesterday(),
            ),
            'status' => TaskStatus::Expired,
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
