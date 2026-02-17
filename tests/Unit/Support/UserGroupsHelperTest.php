<?php

use App\Models\User;
use Ffhs\FfhsTasks\Support\AssignableHelper;
use App\Models\SecondUserGroup;
use App\Models\FirstUserGroup;

describe('hasModels', function () {
    test('returns true when user groups are configured', function () {
        expect(AssignableHelper::hasModels())->toBeTrue();
    });

    test('returns false when no user groups are configured', function () {
        config()->set('ffhs-tasks.assignable_models', []);

        expect(AssignableHelper::hasModels())->toBeFalse();
    });
});

describe('groups', function () {
    test('returns all groups from all configured models', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class, SecondUserGroup::class]);

        FirstUserGroup::factory()->count(2)->create();
        SecondUserGroup::factory()->count(3)->create();

        // Act
        $groups = AssignableHelper::assignables();

        // Assert
        expect($groups)->toHaveCount(5);
    });

    test('filters groups by search term', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class, SecondUserGroup::class]);

        FirstUserGroup::factory()->create(['display_name' => 'Alpha Team']);
        FirstUserGroup::factory()->create(['display_name' => 'Beta Team']);
        SecondUserGroup::factory()->create(['display_name' => 'Alpha Squad']);

        // Act
        $groups = AssignableHelper::assignables('Alpha');

        // Assert
        expect($groups)->toHaveCount(2);
    });
});

describe('groupsForUser', function () {
    test('returns groups the authenticated user belongs to', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();
        $memberGroup = FirstUserGroup::factory()->create();
        $memberGroup->users()->attach($user);

        FirstUserGroup::factory()->create();

        $this->actingAs($user);

        // Act
        $groups = AssignableHelper::assignablesForUser();

        // Assert
        expect($groups)->toHaveCount(1)
            ->and($groups->first()->id)->toBe($memberGroup->id);
    });

    test('accepts a specific user', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();

        $group = FirstUserGroup::factory()->create();
        $group->users()->attach($user);

        // Act
        $groups = AssignableHelper::assignablesForUser($user);

        // Assert
        expect($groups)->toHaveCount(1)
            ->and($groups->first()->id)->toBe($group->id);
    });
});
