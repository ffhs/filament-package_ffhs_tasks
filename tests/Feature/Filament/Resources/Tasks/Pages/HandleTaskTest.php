<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\HandleableTaskType;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Livewire\Livewire;

describe('view or edit header action', function () {
    test('is visible when user can view the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['view' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertActionVisible('view_or_edit');
    });

    test('is hidden when user cannot view the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['view' => false]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertActionHidden('view_or_edit');
    });

    test('links to edit page', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['view' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertActionHasUrl('view_or_edit', TaskResource::getUrl('edit', ['record' => $task]));
    });

    test('shows edit label when user can edit the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['view' => true, 'update' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertActionHasLabel('view_or_edit', __('filament-actions::edit.single.label'));
    });

    test('shows view label when user cannot edit the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['view' => true, 'update' => false]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertActionHasLabel('view_or_edit', __('filament-actions::view.single.label'));
    });
});

describe('authorization', function () {
    test('allows access when user can handle the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertSuccessful();
    });

    test('redirects to index when task is archived', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'status' => TaskStatus::Completed,
        ]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertRedirect(TaskResource::getUrl());
    });
});

describe('form field state', function () {
    test('only handle components are enabled', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [HandleableTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'type' => 'handleable',
        ]);

        TaskPolicy::fake(['update' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertFormFieldIsDisabled('title')
            ->assertFormFieldIsDisabled('description')
            ->assertFormFieldIsDisabled('starts_at')
            ->assertFormFieldIsDisabled('deadline_at')
            ->assertFormFieldIsDisabled('users')
            ->fillForm([
                'data.handled' => true,
                'data.notes' => 'Test notes',
            ])
            ->assertFormSet([
                'data.handled' => true,
                'data.notes' => 'Test notes',
            ]);
    });
});

describe('form configuration', function () {
    test('form has novalidate attribute', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertSee('novalidate');
    });
});

describe('save lifecycle hooks', function () {
    test('calls mutateDataBeforeSave on task type', function () {
        // Arrange
        TestTaskType::resetFlags();
        config()->set('ffhs-tasks.types', [TestTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->fillForm(['title' => 'Updated Title'])
            ->call('save');

        // Assert
        expect(TestTaskType::$mutateDataBeforeSaveCalled)->toBeTrue();
    });

    test('calls afterSave on task type', function () {
        // Arrange
        TestTaskType::resetFlags();
        config()->set('ffhs-tasks.types', [TestTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->fillForm(['title' => 'Updated Title'])
            ->call('save');

        // Assert
        expect(TestTaskType::$afterSaveCalled)->toBeTrue();
    });
});
