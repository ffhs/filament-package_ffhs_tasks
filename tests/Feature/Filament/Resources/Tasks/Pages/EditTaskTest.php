<?php

use App\Models\User;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\EditTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
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

    test('denies access when user cannot view the task', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create();

        TaskPolicy::fake(['view' => false]);

        // Act & Assert
        $this->actingAs($user);

        Livewire::test(EditTask::class, ['record' => $task->id])
            ->assertForbidden();
    });
});

describe('save lifecycle hooks', function () {

    beforeEach(function () {
        TestTaskType::resetFlags();

        config()->set('ffhs-tasks.types', [TestTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);
    });

    test('calls mutateDataBeforeSave on task type', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        TaskPolicy::fake(['update' => true]);

        // Act
        $this->actingAs($user);

        Livewire::test(EditTask::class, ['record' => $task->id])
            ->fillForm(['title' => 'Updated Title'])
            ->call('save');

        // Assert
        expect(TestTaskType::$mutateDataBeforeSaveCalled)->toBeTrue();
    });

    test('calls afterSave on task type', function () {
        // Arrange
        $user = User::factory()->create();
        $task = Task::factory()->create(['type' => TestTaskType::identifier()]);

        TaskPolicy::fake(['update' => true]);

        // Act
        $this->actingAs($user);

        Livewire::test(EditTask::class, ['record' => $task->id])
            ->fillForm(['title' => 'Updated Title'])
            ->call('save');

        // Assert
        expect(TestTaskType::$afterSaveCalled)->toBeTrue();
    });
});
