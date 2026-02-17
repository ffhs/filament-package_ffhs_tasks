<?php

use App\Models\FirstUserGroup;
use App\Models\User;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    config()->set('ffhs-tasks.types', [TestTaskType::class]);
    config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);
});

describe('viewAny()', function () {
    it('allows any user', function () {
        // Arrange
        $user = User::factory()->create();

        // Act & Assert
        expect($user->can('viewAny', Task::class))->toBeTrue();
    });
});

describe('view()', function () {
    it('allows any user to view a task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        // Act & Assert
        expect($user->can('view', $task))->toBeTrue();
    });
});

describe('update()', function () {
    it('allows the creator to update', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'creator_type' => $user->getMorphClass(),
            'creator_id' => $user->getKey(),
        ]);

        // Act & Assert
        expect($user->can('update', $task))->toBeTrue();
    });

    it('allows a directly assigned user to update', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $task->users()->attach($user);

        // Act & Assert
        expect($user->can('update', $task))->toBeTrue();
    });

    it('allows a user assigned through a group to update', function () {
        // Arrange
        $user = User::factory()->create();

        $group = FirstUserGroup::factory()->create();
        $group->users()->attach($user);

        $task = Task::factory()->create();

        Assignable::query()->create([
            'task_id' => $task->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $group->id,
        ]);

        // Act & Assert
        expect($user->can('update', $task))->toBeTrue();
    });

    it('denies an unrelated user', function () {
        // Arrange
        $creator = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create([
            'creator_type' => $creator->getMorphClass(),
            'creator_id' => $creator->getKey(),
        ]);

        // Act & Assert
        expect($otherUser->can('update', $task))->toBeFalse();
    });

    it('denies a user in a different group', function () {
        // Arrange
        $user = User::factory()->create();

        $userGroup = FirstUserGroup::factory()->create();
        $userGroup->users()->attach($user);

        $otherGroup = FirstUserGroup::factory()->create();

        $task = Task::factory()->create();

        Assignable::query()->create([
            'task_id' => $task->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $otherGroup->id,
        ]);

        // Act & Assert
        expect($user->can('update', $task))->toBeFalse();
    });

    it('allows a user with updateAny permission', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        Gate::define('updateAny', fn (User $user, Task $task) => true);

        // Act & Assert
        expect($user->can('update', $task))->toBeTrue();
    });
});

describe('handle()', function () {
    it('allows any user to handle a task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        // Act & Assert
        expect($user->can('handle', $task))->toBeTrue();
    });
});
