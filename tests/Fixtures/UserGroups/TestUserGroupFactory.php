<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\UserGroups;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestUserGroup>
 */
class TestUserGroupFactory extends Factory
{
    protected $model = TestUserGroup::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'display_name' => fake()->unique()->company(),
        ];
    }
}
