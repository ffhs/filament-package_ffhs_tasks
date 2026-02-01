<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListTasks;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Policies\TaskPolicy;

use function Pest\Livewire\livewire;

describe('my tab', function () {
    test('shows tasks assigned to the authenticated user', function () {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $assignedTask = Task::factory()->create();
        $assignedTask->users()->attach($user);

        $unassignedTask = Task::factory()->create();
        $unassignedTask->users()->attach($otherUser);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'my')
            ->assertCanSeeTableRecords([$assignedTask])
            ->assertCanNotSeeTableRecords([$unassignedTask]);
    });

    test('shows empty table when user has no assigned tasks', function () {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create();
        $task->users()->attach($otherUser);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'my')
            ->assertCanNotSeeTableRecords([$task]);
    });

    test('shows multiple tasks assigned to the authenticated user', function () {
        // Arrange
        $user = User::factory()->create();

        $tasks = Task::factory()
            ->count(3)
            ->create();

        $tasks->each(fn (Task $task) => $task->users()->attach($user));

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'my')
            ->assertCanSeeTableRecords($tasks);
    });
});

describe('row styling', function () {
    test('applies opacity-50 class to tasks that have not started yet', function () {
        // Arrange
        $user = User::factory()->create();

        $futureTask = Task::factory()->create([
            'starts_at' => now()->addDays(5),
        ]);
        $futureTask->users()->attach($user);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'my')
            ->assertSeeHtml('opacity-50');
    });

    test('does not apply opacity-50 class to tasks that have already started', function () {
        // Arrange
        $user = User::factory()->create();

        $startedTask = Task::factory()->create([
            'starts_at' => now()->subDays(1),
        ]);
        $startedTask->users()->attach($user);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'my')
            ->assertDontSeeHtml('opacity-50');
    });

    test('does not apply opacity-50 class to tasks without a start date', function () {
        // Arrange
        $user = User::factory()->create();

        $taskWithoutStartDate = Task::factory()->create([
            'starts_at' => null,
        ]);
        $taskWithoutStartDate->users()->attach($user);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'my')
            ->assertDontSeeHtml('opacity-50');
    });
});

describe('table actions', function () {
    describe('view or edit action', function () {
        test('is visible when user can view the task', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['view' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionVisible('view_or_edit', $task);
        });

        test('is hidden when user cannot view the task', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['view' => false]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionHidden('view_or_edit', $task);
        });

        test('shows edit label when user can edit the task', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['view' => true, 'update' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionHasLabel('view_or_edit', __('filament-actions::edit.single.label'), $task);
        });

        test('shows view label when user cannot edit the task', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['view' => true, 'update' => false]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionHasLabel('view_or_edit', __('filament-actions::view.single.label'), $task);
        });
    });

    describe('handle action', function () {
        test('is visible when user can handle the task', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['handle' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionVisible('handle', $task);
        });

        test('is hidden when user cannot handle the task', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['handle' => false]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionHidden('handle', $task);
        });

        test('is hidden when task has not started yet', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create([
                'starts_at' => now()->addDays(5),
            ]);
            $task->users()->attach($user);

            TaskPolicy::fake(['handle' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionHidden('handle', $task);
        });

        test('is visible when task has already started', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create([
                'starts_at' => now()->subDays(1),
            ]);
            $task->users()->attach($user);

            TaskPolicy::fake(['handle' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionVisible('handle', $task);
        });

        test('is visible when task has no start date', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create([
                'starts_at' => null,
            ]);
            $task->users()->attach($user);

            TaskPolicy::fake(['handle' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionVisible('handle', $task);
        });
    });
});

describe('created tab', function () {
    test('shows tasks created by the authenticated user', function () {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $createdTask = Task::factory()->create([
            'creator_type' => $user::class,
            'creator_id' => $user->id,
        ]);

        $otherTask = Task::factory()->create([
            'creator_type' => $otherUser::class,
            'creator_id' => $otherUser->id,
        ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'created')
            ->assertCanSeeTableRecords([$createdTask])
            ->assertCanNotSeeTableRecords([$otherTask]);
    });

    test('shows empty table when user has not created any tasks', function () {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create([
            'creator_type' => $otherUser::class,
            'creator_id' => $otherUser->id,
        ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'created')
            ->assertCanNotSeeTableRecords([$task]);
    });

    test('shows multiple tasks created by the authenticated user', function () {
        // Arrange
        $user = User::factory()->create();

        $tasks = Task::factory()
            ->count(3)
            ->create([
                'creator_type' => $user::class,
                'creator_id' => $user->id,
            ]);

        // Act & Assert
        $this->actingAs($user);

        livewire(ListTasks::class)
            ->set('activeTab', 'created')
            ->assertCanSeeTableRecords($tasks);
    });
});
