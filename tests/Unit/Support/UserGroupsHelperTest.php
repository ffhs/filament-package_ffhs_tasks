<?php

use App\Models\User;
use Ffhs\FfhsTasks\Support\UserGroupsHelper;
use Ffhs\FfhsTasks\Tests\Fixtures\UserGroups\AnotherTestUserGroup;
use Ffhs\FfhsTasks\Tests\Fixtures\UserGroups\TestUserGroup;

describe('hasModels', function () {
    test('returns true when user groups are configured', function () {
        expect(UserGroupsHelper::hasModels())->toBeTrue();
    });

    test('returns false when no user groups are configured', function () {
        config()->set('ffhs-tasks.user_groups', []);

        expect(UserGroupsHelper::hasModels())->toBeFalse();
    });
});

describe('groups', function () {
    test('returns all groups from all configured models', function () {
        // Arrange
        config()->set('ffhs-tasks.user_groups', [TestUserGroup::class, AnotherTestUserGroup::class]);

        TestUserGroup::factory()->count(2)->create();
        AnotherTestUserGroup::factory()->count(3)->create();

        // Act
        $groups = UserGroupsHelper::groups();

        // Assert
        expect($groups)->toHaveCount(5);
    });

    test('filters groups by search term', function () {
        // Arrange
        config()->set('ffhs-tasks.user_groups', [TestUserGroup::class, AnotherTestUserGroup::class]);

        TestUserGroup::factory()->create(['display_name' => 'Alpha Team']);
        TestUserGroup::factory()->create(['display_name' => 'Beta Team']);
        AnotherTestUserGroup::factory()->create(['display_name' => 'Alpha Squad']);

        // Act
        $groups = UserGroupsHelper::groups('Alpha');

        // Assert
        expect($groups)->toHaveCount(2);
    });
});

describe('groupsForUser', function () {
    test('returns groups the authenticated user belongs to', function () {
        // Arrange
        config()->set('ffhs-tasks.user_groups', [TestUserGroup::class]);

        $user = User::factory()->create();
        $memberGroup = TestUserGroup::factory()->create();
        $memberGroup->users()->attach($user);

        TestUserGroup::factory()->create();

        $this->actingAs($user);

        // Act
        $groups = UserGroupsHelper::groupsForUser();

        // Assert
        expect($groups)->toHaveCount(1)
            ->and($groups->first()->id)->toBe($memberGroup->id);
    });

    test('accepts a specific user', function () {
        // Arrange
        config()->set('ffhs-tasks.user_groups', [TestUserGroup::class]);

        $user = User::factory()->create();

        $group = TestUserGroup::factory()->create();
        $group->users()->attach($user);

        // Act
        $groups = UserGroupsHelper::groupsForUser($user);

        // Assert
        expect($groups)->toHaveCount(1)
            ->and($groups->first()->id)->toBe($group->id);
    });
});
