<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListAllTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListTasks;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Policies\TaskPolicy;

use function Pest\Livewire\livewire;

describe('visibility', function () {
    test('is visible when user is assigned to the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $task->users()->attach($user);

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'my')
            ->assertTableActionVisible('unassign_me', $task);
    });

    test('is hidden when user is not assigned to the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertTableActionHidden('unassign_me', $task);
    });

    test('is hidden when task is archived', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->completed()->create();
        $task->users()->attach($user);

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListAllTasks::class)
            ->assertTableActionHidden('unassign_me', $task);
    });
});

describe('action', function () {
    test('detaches the authenticated user from the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $task->users()->attach($user);

        TaskPolicy::fake(['update' => true]);

        // Act
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'my')
            ->callTableAction('unassign_me', $task);

        // Assert
        expect($task->users()->pluck('users.id'))->not->toContain($user->id);
    });

    test('does not affect other assigned users', function () {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create();
        $task->users()->attach([$user->id, $otherUser->id]);

        TaskPolicy::fake(['update' => true]);

        // Act
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'my')
            ->callTableAction('unassign_me', $task);

        // Assert
        expect($task->users()->pluck('users.id'))
            ->not->toContain($user->id)
            ->toContain($otherUser->id);
    });
});
