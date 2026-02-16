<?php

namespace Database\Factories;

use App\Models\FirstUserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\Models\FirstUserGroup
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class FirstUserGroupFactory extends Factory
{
    /**
     * @var class-string<TModel>
     */
    protected $model = FirstUserGroup::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'display_name' => '[Group Type 1] '. fake()->colorName(),
        ];
    }
}
