<?php

namespace Database\Factories;

use App\Models\SecondUserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of \App\Models\SecondUserGroup
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class SecondUserGroupFactory extends Factory
{
    /**
     * @var class-string<TModel>
     */
    protected $model = SecondUserGroup::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'display_name' => '[Group Type 2] '. fake()->colorName(),
        ];
    }
}
