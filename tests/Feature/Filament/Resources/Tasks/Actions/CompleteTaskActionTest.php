<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    TestTaskType::resetFlags();

    config()->set('ffhs-tasks.types', [TestTaskType::class]);
    config()->set('ffhs-tasks.user_groups', []);
});

describe('action', function () {
    test('completes the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('complete')->schemaComponent('form-actions', schema: 'content')
            );

        // Assert
        $task->refresh();

        expect($task->status)->toBe(TaskStatus::Completed)
            ->and($task->completed_at)->not->toBeNull();
    });

    test('shows success notification', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('complete')->schemaComponent('form-actions', schema: 'content')
            )
            ->assertNotified();
    });

    test('redirects to task index', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => 'test-1']);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('complete')->schemaComponent('form-actions', schema: 'content')
            )
            ->assertRedirect();
    });

    test('calls mutateDataBeforeComplete on task type', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('complete')->schemaComponent('form-actions', schema: 'content')
            );

        // Assert
        expect(TestTaskType::$mutateDataBeforeCompleteCalled)->toBeTrue();
    });

    test('calls afterComplete on task type', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('complete')->schemaComponent('form-actions', schema: 'content')
            );

        // Assert
        expect(TestTaskType::$afterCompleteCalled)->toBeTrue();
    });
});

describe('validation', function () {
    test('does not mount action when form has validation errors', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->fillForm(['title' => ''])
            ->callAction(
                TestAction::make('complete')->schemaComponent('form-actions', schema: 'content')
            )
            ->assertActionNotMounted();
    });

    test('does not complete the task when validation fails', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->fillForm(['title' => ''])
            ->callAction(
                TestAction::make('complete')->schemaComponent('form-actions', schema: 'content')
            );

        // Assert
        $task->refresh();

        expect($task->status)->toBe(TaskStatus::InProgress)
            ->and($task->completed_at)->toBeNull();
    });
});
