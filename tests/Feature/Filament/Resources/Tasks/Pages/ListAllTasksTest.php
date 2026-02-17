<?php

use App\Models\FirstUserGroup;
use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskPrivacy;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListAllTasks;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\Watchable;

use function Pest\Livewire\livewire;

describe('filtering', function () {
    test('shows tasks with all statuses', function () {
        // Arrange
        $user = User::factory()->create();

        $inProgressTask = Task::factory()->create(['status' => TaskStatus::InProgress]);
        $completedTask = Task::factory()->completed()->create();
        $cancelledTask = Task::factory()->cancelled()->create();
        $expiredTask = Task::factory()->expired()->create();

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertCanSeeTableRecords([
                $inProgressTask,
                $completedTask,
                $cancelledTask,
                $expiredTask,
            ]);
    });
});

describe('privacy', function () {
    test('shows public tasks to any user', function () {
        // Arrange
        $user = User::factory()->create();
        $publicTask = Task::factory()->create([
            'privacy' => TaskPrivacy::Public
        ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertCanSeeTableRecords([$publicTask]);
    });

    test('hides private tasks from unrelated users', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();
        $privateTask = Task::factory()
            ->private()
            ->create([
                'creator_type' => null,
                'creator_id' => null
            ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertCanNotSeeTableRecords([$privateTask]);
    });

    test('shows private tasks to the creator', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $privateTask = Task::factory()
            ->private()
            ->create();

        // Act & Assert
        livewire(ListAllTasks::class)
            ->assertCanSeeTableRecords([$privateTask]);
    });

    test('shows private tasks to an assigned user', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();

        $group = FirstUserGroup::factory()->create();
        $group->users()->attach($user);

        $privateTask = Task::factory()
            ->private()
            ->create(['creator_type' => null, 'creator_id' => null]);

        Assignable::query()->create([
            'task_id' => $privateTask->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $group->id,
        ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertCanSeeTableRecords([$privateTask]);
    });

    test('shows private tasks to a watching user', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();

        $group = FirstUserGroup::factory()->create();
        $group->users()->attach($user);

        $privateTask = Task::factory()
            ->private()
            ->create(['creator_type' => null, 'creator_id' => null]);

        Watchable::query()->create([
            'task_id' => $privateTask->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $group->id,
        ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertCanSeeTableRecords([$privateTask]);
    });

    test('shows private tasks to a directly assigned user', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [User::class]);

        $user = User::factory()->create();

        $privateTask = Task::factory()
            ->private()
            ->create(['creator_type' => null, 'creator_id' => null]);

        Assignable::query()->create([
            'task_id' => $privateTask->id,
            'assignable_type' => User::class,
            'assignable_id' => $user->id,
        ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertCanSeeTableRecords([$privateTask]);
    });

    test('shows private tasks to a directly watching user', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [User::class]);

        $user = User::factory()->create();

        $privateTask = Task::factory()
            ->private()
            ->create(['creator_type' => null, 'creator_id' => null]);

        Watchable::query()->create([
            'task_id' => $privateTask->id,
            'assignable_type' => User::class,
            'assignable_id' => $user->id,
        ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertCanSeeTableRecords([$privateTask]);
    });

    test('does not show private tasks from groups the user does not belong to', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherGroup = FirstUserGroup::factory()->create();
        $otherGroup->users()->attach($otherUser);

        $privateTask = Task::factory()
            ->private()
            ->create(['creator_type' => null, 'creator_id' => null]);

        Assignable::query()->create([
            'task_id' => $privateTask->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $otherGroup->id,
        ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertCanNotSeeTableRecords([$privateTask]);
    });
});
