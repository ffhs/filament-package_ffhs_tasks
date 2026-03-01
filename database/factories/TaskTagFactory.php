<?php

namespace Ffhs\FfhsTasks\Database\Factories;

use Ffhs\FfhsTasks\Models\TaskTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskTagFactory extends Factory
{
    protected $model = TaskTag::class;

    public function definition(): array
    {
        return [
            'display_name' => $this->faker->colorName(),
        ];
    }
}
