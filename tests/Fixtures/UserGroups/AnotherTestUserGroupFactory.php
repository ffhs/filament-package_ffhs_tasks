<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\UserGroups;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnotherTestUserGroup>
 */
class AnotherTestUserGroupFactory extends Factory
{
    protected $model = AnotherTestUserGroup::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'display_name' => fake()->unique()->company(),
        ];
    }
}
