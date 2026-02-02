<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach()->skip();

describe('action', function () {
    test('completes the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

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
        $task = Task::factory()->create();

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
        $task = Task::factory()->create();

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('complete')->schemaComponent('form-actions', schema: 'content')
            )
            ->assertRedirect();
    });

    test('calls afterComplete on task type', function () {
        // Arrange
        TestTaskType::$afterCompleteCalled = false;
        config(['ffhs-tasks.types' => [TestTaskType::class]]);

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
