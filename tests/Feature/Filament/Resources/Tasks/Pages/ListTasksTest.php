<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListAllTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use App\Models\SecondUserGroup;
use App\Models\FirstUserGroup;
use pxlrbt\LaravelAssertDom\Assertions\DomAssert;

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
    describe('record url', function () {
        test('links to handle by default', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['view' => true, 'update' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListTasks::class)
                ->assertDom(
                    '.fi-ta-cell > a',
                    fn (DomAssert $el) => $el
                        ->attribute('href')->toEqual(TaskResource::getUrl('handle', ['record' => $task]))
                )
                ->assertDomAll(
                    '.fi-ta-cell > a',
                    fn (DomAssert $el) => $el
                        ->attribute('href')->not->toEqual(TaskResource::getUrl('edit', ['record' => $task]))
                );
        });

        test('links to view/edit when archived', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->completed()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['view' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListAllTasks::class)
                ->assertDom(
                    '.fi-ta-cell > a',
                    fn (DomAssert $el) => $el
                        ->attribute('href')->toEqual(TaskResource::getUrl('edit', ['record' => $task]))
                )
                ->assertDomAll(
                    '.fi-ta-cell > a',
                    fn (DomAssert $el) => $el
                        ->attribute('href')->not->toEqual(TaskResource::getUrl('handle', ['record' => $task]))
                );
        });
    });

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

        test('does not show edit label when task is archived', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->completed()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['view' => true, 'update' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListAllTasks::class)
                ->set('activeTab', 'my')
                ->assertTableActionDoesNotHaveLabel('view_or_edit', __('filament-actions::edit.single.label'), $task)
                ->assertTableActionHasLabel('view_or_edit', __('filament-actions::view.single.label'), $task);
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

        test('is hidden when task is archived', function () {
            // Arrange
            $user = User::factory()->create();
            $task = Task::factory()->cancelled()->create();
            $task->users()->attach($user);

            TaskPolicy::fake(['handle' => true]);

            // Act & Assert
            $this->actingAs($user);

            livewire(ListAllTasks::class)
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

describe('assigned tab', function () {
    test('is visible when assignable models are configured', function () {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act & Assert
        livewire(ListTasks::class)
            ->assertSeeText(__('ffhs-tasks::pages.index.tabs.assigned'));
    });

    test('is hidden when no assignable models are configured', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', []);
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act & Assert
        livewire(ListTasks::class)
            ->assertDontSeeText(__('ffhs-tasks::pages.index.tabs.assigned'));
    });

    test('shows tasks belonging to the assignables', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();

        $group = FirstUserGroup::factory()->create();
        $group->users()->attach($user);

        $taskInGroup = Task::factory()->create();

        Assignable::query()->create([
            'task_id' => $taskInGroup->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $group->id,
        ]);

        $taskOutsideGroup = Task::factory()->create();

        $this->actingAs($user);

        // Act & Assert
        livewire(ListTasks::class)
            ->set('activeTab', 'assigned')
            ->assertCanSeeTableRecords([$taskInGroup])
            ->assertCanNotSeeTableRecords([$taskOutsideGroup]);
    });

    test('shows tasks from multiple assignable types', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class, SecondUserGroup::class]);

        $user = User::factory()->create();

        $firstGroup = FirstUserGroup::factory()->create();
        $firstGroup->users()->attach($user);

        $secondGroup = SecondUserGroup::factory()->create();
        $secondGroup->users()->attach($user);

        $taskA = Task::factory()->create();

        Assignable::query()->create([
            'task_id' => $taskA->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $firstGroup->id,
        ]);

        $taskB = Task::factory()->create();

        Assignable::query()->create([
            'task_id' => $taskB->id,
            'assignable_type' => SecondUserGroup::class,
            'assignable_id' => $secondGroup->id,
        ]);

        $this->actingAs($user);

        // Act & Assert
        livewire(ListTasks::class)
            ->set('activeTab', 'assigned')
            ->assertCanSeeTableRecords([$taskA, $taskB]);
    });

    test('does not show tasks from assignables the user does not belong to', function () {
        // Arrange
        config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userGroup = FirstUserGroup::factory()->create();
        $userGroup->users()->attach($user);

        $otherGroup = FirstUserGroup::factory()->create();
        $otherGroup->users()->attach($otherUser);

        $userTask = Task::factory()->create();

        Assignable::query()->create([
            'task_id' => $userTask->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $userGroup->id,
        ]);

        $otherTask = Task::factory()->create();

        Assignable::query()->create([
            'task_id' => $otherTask->id,
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $otherGroup->id,
        ]);

        $this->actingAs($user);

        // Act & Assert
        livewire(ListTasks::class)
            ->set('activeTab', 'assigned')
            ->assertCanSeeTableRecords([$userTask])
            ->assertCanNotSeeTableRecords([$otherTask]);
    });

    test('shows empty table when user has no assigned tasks', function () {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act & Assert
        livewire(ListTasks::class)
            ->set('activeTab', 'assigned')
            ->assertCountTableRecords(0);
    });
});
