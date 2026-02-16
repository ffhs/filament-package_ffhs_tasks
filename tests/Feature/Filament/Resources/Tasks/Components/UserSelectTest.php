<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Components\UserSelect;
use Ffhs\FfhsTasks\Tests\Fixtures\UserGroups\TestUserGroup;

describe('getUsersFromGroups', function () {
    test('returns users from selected user groups', function () {
        // Arrange
        $users = User::factory()->count(3)->create();
        $group = TestUserGroup::factory()->create();
        $group->users()->attach($users->take(2));

        $compositeValues = [TestUserGroup::class . ':::' . $group->id];

        // Act
        $result = invade(new UserSelect())->getUsersFromGroups($compositeValues);

        // Assert
        expect($result)->toHaveCount(2)
            ->and($result->pluck('id')->all())->toContain($users[0]->id, $users[1]->id);
    });

    test('filters users by search term', function () {
        // Arrange
        $alice = User::factory()->create(['name' => 'Alice Smith']);
        $bob = User::factory()->create(['name' => 'Bob Jones']);
        $alice2 = User::factory()->create(['name' => 'Alice Wonderland']);

        $group = TestUserGroup::factory()->create();
        $group->users()->attach([$alice->id, $bob->id, $alice2->id]);

        $compositeValues = [TestUserGroup::class . ':::' . $group->id];

        // Act
        $users = invade(new UserSelect())->getUsersFromGroups($compositeValues, 'Alice');

        // Assert
        expect($users)->toHaveCount(2)
            ->and($users->pluck('name')->all())->each->toContain('Alice');
    });

    test('returns empty collection when no groups are selected', function () {
        // Act
        $users = invade(new UserSelect())->getUsersFromGroups([]);

        // Assert
        expect($users)->toBeEmpty();
    });

    test('deduplicates users that appear in multiple groups', function () {
        // Arrange
        $sharedUser = User::factory()->create();
        $groupA = TestUserGroup::factory()->create();
        $groupA->users()->attach($sharedUser);
        $groupB = TestUserGroup::factory()->create();
        $groupB->users()->attach($sharedUser);

        $compositeValues = [
            TestUserGroup::class . ':::' . $groupA->id,
            TestUserGroup::class . ':::' . $groupB->id,
        ];

        // Act
        $result = invade(new UserSelect())->getUsersFromGroups($compositeValues);

        // Assert
        expect($result->pluck('id'))->toHaveCount(1);
    });
});
