<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\ViewOrEditAction;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;

describe('canEdit', function () {
    test('returns false when task is archived', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);

        $task = Task::factory()->create([
            'type' => TestTaskType::identifier(),
            'status' => TaskStatus::Completed,
        ]);

        // Act
        $result = ViewOrEditAction::canEdit($task);

        // Assert
        expect($result)->toBeFalse();
    });

    test('returns false when task is cancelled', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);

        $task = Task::factory()->create([
            'type' => TestTaskType::identifier(),
            'status' => TaskStatus::Cancelled,
        ]);

        // Act
        $result = ViewOrEditAction::canEdit($task);

        // Assert
        expect($result)->toBeFalse();
    });

    test('delegates to task type when not archived', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'type' => TestTaskType::identifier(),
            'status' => TaskStatus::InProgress,
        ]);

        TaskPolicy::fake(['update' => true]);

        $this->actingAs($user);

        // Act
        $result = ViewOrEditAction::canEdit($task);

        // Assert
        expect($result)->toBeTrue();
    });
});
