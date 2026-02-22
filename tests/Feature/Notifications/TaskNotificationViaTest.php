<?php

use App\Models\User;
use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskAssignedNotification;
use Ffhs\FfhsTasks\Notifications\TaskDeadlineApproachingNotification;
use Ffhs\FfhsTasks\Notifications\TaskDeadlineExceededNotification;
use Ffhs\FfhsTasks\Notifications\TaskStartDateReachedNotification;
use Ffhs\FfhsTasks\Notifications\TaskStatusChangedNotification;

describe('notification via channels', function () {
    it('returns empty array when notifications are disabled', function (string $notificationClass) {
        // Arrange
        config()->set('ffhs-tasks.notifications.enabled', []);

        $task = Task::factory()->create();
        $user = User::factory()->create();

        $notification = match ($notificationClass) {
            TaskDeadlineApproachingNotification::class => new $notificationClass($task, CarbonInterval::days(3)),
            TaskDeadlineExceededNotification::class => new $notificationClass($task, CarbonInterval::hours(0)),
            default => new $notificationClass($task),
        };

        // Act
        $channels = $notification->via($user);

        // Assert
        expect($channels)->toBe([]);
    })->with([
        'TaskAssignedNotification' => TaskAssignedNotification::class,
        'TaskDeadlineApproachingNotification' => TaskDeadlineApproachingNotification::class,
        'TaskDeadlineExceededNotification' => TaskDeadlineExceededNotification::class,
        'TaskStatusChangedNotification' => TaskStatusChangedNotification::class,
        'TaskStartDateReachedNotification' => TaskStartDateReachedNotification::class,
    ]);

    it('returns mail channel when notification is enabled', function (string $notificationClass) {
        // Arrange
        config()->set('ffhs-tasks.notifications.enabled', [$notificationClass]);

        $task = Task::factory()->create();
        $user = User::factory()->create();

        $notification = match ($notificationClass) {
            TaskDeadlineApproachingNotification::class => new $notificationClass($task, CarbonInterval::days(3)),
            TaskDeadlineExceededNotification::class => new $notificationClass($task, CarbonInterval::hours(0)),
            default => new $notificationClass($task),
        };

        // Act
        $channels = $notification->via($user);

        // Assert
        expect($channels)->toBe(['mail']);
    })->with([
        'TaskAssignedNotification' => TaskAssignedNotification::class,
        'TaskDeadlineApproachingNotification' => TaskDeadlineApproachingNotification::class,
        'TaskDeadlineExceededNotification' => TaskDeadlineExceededNotification::class,
        'TaskStatusChangedNotification' => TaskStatusChangedNotification::class,
        'TaskStartDateReachedNotification' => TaskStartDateReachedNotification::class,
    ]);
});
