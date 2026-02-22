<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskStatusChangedNotification;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    config()->set('ffhs-tasks.notifications.enabled', [TaskStatusChangedNotification::class]);
    config()->set('ffhs-tasks.types', [TestTaskType::class]);
});

describe('SendStatusChangedNotification', function () {
    it('sends notification to assignees when task is completed', function () {
        // Arrange
        Notification::fake();

        $actor = User::factory()->create();
        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assignee->id,
        ]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $actor->id,
        ]);

        $this->actingAs($actor);

        // Act
        $task->complete();

        // Assert
        Notification::assertSentTo($assignee, TaskStatusChangedNotification::class);
        Notification::assertNotSentTo($actor, TaskStatusChangedNotification::class);
    });

    it('sends notification to assignees when task is cancelled', function () {
        // Arrange
        Notification::fake();

        $actor = User::factory()->create();
        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assignee->id,
        ]);

        $this->actingAs($actor);

        // Act
        $task->cancel();

        // Assert
        Notification::assertSentTo($assignee, TaskStatusChangedNotification::class);
    });

    it('sends notification to assignees when task is expired', function () {
        // Arrange
        Notification::fake();

        $actor = User::factory()->create();
        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assignee->id,
        ]);

        $this->actingAs($actor);

        // Act
        $task->expire();

        // Assert
        Notification::assertSentTo($assignee, TaskStatusChangedNotification::class);
    });

    it('does not send notification when status changes to InProgress', function () {
        // Arrange
        Notification::fake();

        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assignee->id,
        ]);

        // Act — update to same status
        $task->update(['status' => TaskStatus::InProgress]);

        // Assert
        Notification::assertNotSentTo($assignee, TaskStatusChangedNotification::class);
    });

    it('does not send notifications when disabled in config', function () {
        // Arrange
        config()->set('ffhs-tasks.notifications.enabled', []);
        Notification::fake();

        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assignee->id,
        ]);

        // Act
        $task->complete();

        // Assert
        Notification::assertNotSentTo($assignee, TaskStatusChangedNotification::class);
    });
});
