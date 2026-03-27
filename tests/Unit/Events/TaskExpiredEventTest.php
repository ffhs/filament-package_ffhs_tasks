<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\TaskExpiredEvent;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    config()->set('ffhs-tasks.types', [TestTaskType::class]);
});

describe('TaskExpiredEvent', function () {
    it('is dispatched when a task expires', function () {
        // Arrange
        Event::fake([TaskExpiredEvent::class]);

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subMinute(),
            'expires_after_deadline' => true,
        ]);

        // Act
        $task->expire();

        // Assert
        Event::assertDispatched(TaskExpiredEvent::class);
    });

    it('carries the task model', function () {
        // Arrange
        Event::fake([TaskExpiredEvent::class]);

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subMinute(),
            'expires_after_deadline' => true,
        ]);

        // Act
        $task->expire();

        // Assert
        Event::assertDispatched(TaskExpiredEvent::class, function (TaskExpiredEvent $event) use ($task) {
            return $event->task->id === $task->id;
        });
    });
});
