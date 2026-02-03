<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\HandleableTaskType;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\ValidationTaskType;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

describe('action', function () {
    test('saves the task handle data', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [HandleableTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'type' => 'handleable',
            'data' => ['handled' => false, 'notes' => ''],
        ]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->fillForm([
                'data.handled' => true,
                'data.notes' => 'Test notes',
            ])
            ->callAction(
                TestAction::make('save_without_validation')->schemaComponent('form-actions', schema: 'content')
            );

        // Assert
        $task->refresh();

        expect($task->data)->toMatchArray([
            'handled' => true,
            'notes' => 'Test notes',
        ]);
    });

    test('shows success notification', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [HandleableTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => 'handleable']);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->callAction(
                TestAction::make('save_without_validation')->schemaComponent('form-actions', schema: 'content')
            )
            ->assertNotified();
    });
});

describe('validation bypass', function () {
    test('saves when required field is empty', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [ValidationTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'type' => ValidationTaskType::identifier(),
            'data' => ['required_field' => 'original value'],
        ]);

        // Act
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->fillForm(['data.required_field' => ''])
            ->callAction(
                TestAction::make('save_without_validation')->schemaComponent('form-actions', schema: 'content')
            )
            ->assertHasNoFormErrors();

        // Assert
        $task->refresh();

        expect($task->data['required_field'] ?? null)->toBeEmpty();
    });

    test('does not show validation errors', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [ValidationTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'type' => ValidationTaskType::identifier(),
        ]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(HandleTask::class, ['record' => $task->id])
            ->fillForm(['data.required_field' => ''])
            ->callAction(
                TestAction::make('save_without_validation')->schemaComponent('form-actions', schema: 'content')
            )
            ->assertHasNoFormErrors()
            ->assertNotified();
    });
});
