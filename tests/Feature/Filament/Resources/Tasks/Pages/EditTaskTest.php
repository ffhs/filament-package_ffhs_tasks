<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\EditTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use Livewire\Livewire;

describe('handle header action', function () {
    test('is visible when user can handle the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake([
            'update' => true,
            'handle' => true,
        ]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(EditTask::class, ['record' => $task->id])
            ->assertActionVisible('handle');
    });

    test('is hidden when user cannot handle the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake([
            'update' => true,
            'handle' => false,
        ]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(EditTask::class, ['record' => $task->id])
            ->assertActionHidden('handle');
    });

    test('links to handle page', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake([
            'update' => true,
            'handle' => true,
        ]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(EditTask::class, ['record' => $task->id])
            ->assertActionHasUrl('handle', TaskResource::getUrl('handle', ['record' => $task]));
    });
});

describe('authorization', function () {
    test('allows access when user can update the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(EditTask::class, ['record' => $task->id])
            ->assertSuccessful();
    });

    test('denies access when user cannot update the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['update' => false]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(EditTask::class, ['record' => $task->id])
            ->assertForbidden();
    });

    test('redirects to index when task is archived', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'status' => TaskStatus::Completed,
        ]);

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(EditTask::class, ['record' => $task->id])
            ->assertRedirect(TaskResource::getUrl());
    });
});
