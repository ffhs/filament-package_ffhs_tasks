<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListAllTasks;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Policies\TaskPolicy;

use function Pest\Livewire\livewire;

describe('visibility', function () {
    test('is visible when user is not assigned to the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertTableActionVisible('assign_me', $task);
    });

    test('is hidden when user is already assigned to the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $task->users()->attach($user);

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertTableActionHidden('assign_me', $task);
    });

    test('is hidden when task is archived', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->completed()->create();

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertTableActionHidden('assign_me', $task);
    });
});

describe('action', function () {
    test('assigns the authenticated user to the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['update' => true]);

        // Act
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->callTableAction('assign_me', $task);

        // Assert
        expect($task->users()->pluck('users.id'))->toContain($user->id);
    });
});
