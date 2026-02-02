<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;

describe('visibility', function () {
    test('is visible when task can be cancelled', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['can_be_cancelled' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertActionVisible(
                TestAction::make('cancel')->schemaComponent('form-actions', schema: 'content')
            );
    });

    test('is not rendered when task cannot be cancelled', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['can_be_cancelled' => false]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->assertActionDoesNotExist(
                TestAction::make('cancel')->schemaComponent('form-actions', schema: 'content')
            );
    });
});

describe('action', function () {
    test('cancels the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['can_be_cancelled' => true]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('cancel')->schemaComponent('form-actions', schema: 'content')
            );

        // Assert
        $task->refresh();

        expect($task->status)->toBe(TaskStatus::Cancelled)
            ->and($task->cancelled_at)->not->toBeNull();
    });

    test('shows success notification', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['can_be_cancelled' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('cancel')->schemaComponent('form-actions', schema: 'content')
            )
            ->assertNotified();
    });

    test('redirects to task index', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['can_be_cancelled' => true]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('cancel')->schemaComponent('form-actions', schema: 'content')
            )
            ->assertRedirect();
    });

    test('calls mutateDataBeforeCancel on task type', function () {
        // Arrange
        TestTaskType::resetFlags();
        config(['ffhs-tasks.types' => [TestTaskType::class]]);

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'can_be_cancelled' => true,
            'type' => TestTaskType::identifier(),
        ]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('cancel')->schemaComponent('form-actions', schema: 'content')
            );

        // Assert
        expect(TestTaskType::$mutateDataBeforeCancelCalled)->toBeTrue();
    });

    test('calls afterCancel on task type', function () {
        // Arrange
        TestTaskType::resetFlags();
        config(['ffhs-tasks.types' => [TestTaskType::class]]);

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'can_be_cancelled' => true,
            'type' => TestTaskType::identifier(),
        ]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('cancel')->schemaComponent('form-actions', schema: 'content')
            );

        // Assert
        expect(TestTaskType::$afterCancelCalled)->toBeTrue();
    });
});
