<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\TaskStartedEvent;
use Ffhs\FfhsTasks\Jobs\DispatchTaskStartedEventJob;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    config()->set('ffhs-tasks.types', [TestTaskType::class]);
});

describe('TaskStartedEvent', function () {
    it('is dispatched when a task reaches its start date', function () {
        // Arrange
        Event::fake([TaskStartedEvent::class]);

        Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'starts_at' => Carbon::now(),
        ]);

        // Act
        (new DispatchTaskStartedEventJob())->handle();

        // Assert
        Event::assertDispatched(TaskStartedEvent::class);
    });

    it('is not dispatched when start date is in the future', function () {
        // Arrange
        Event::fake([TaskStartedEvent::class]);

        Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'starts_at' => Carbon::now()->addSecond(),
        ]);

        // Act
        (new DispatchTaskStartedEventJob())->handle();

        // Assert
        Event::assertNotDispatched(TaskStartedEvent::class);
    });

    it('is not dispatched twice for the same task', function () {
        // Arrange
        Event::fake([TaskStartedEvent::class]);

        Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'starts_at' => Carbon::now()->subMinute(),
        ]);

        // Act
        (new DispatchTaskStartedEventJob())->handle();
        (new DispatchTaskStartedEventJob())->handle();

        // Assert
        Event::assertDispatchedTimes(TaskStartedEvent::class, 1);
    });

    it('is not dispatched for non-InProgress tasks', function (TaskStatus $status) {
        // Arrange
        Event::fake([TaskStartedEvent::class]);

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'starts_at' => Carbon::now()->subMinute(),
        ]);
        $task->update(['status' => $status]);

        // Act
        (new DispatchTaskStartedEventJob())->handle();

        // Assert
        Event::assertNotDispatched(TaskStartedEvent::class);
    })->with([
        'completed' => TaskStatus::Completed,
        'cancelled' => TaskStatus::Cancelled,
        'expired' => TaskStatus::Expired,
    ]);

    it('carries the task model', function () {
        // Arrange
        Event::fake([TaskStartedEvent::class]);

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'starts_at' => Carbon::now()->subMinute(),
        ]);

        // Act
        (new DispatchTaskStartedEventJob())->handle();

        // Assert
        Event::assertDispatched(TaskStartedEvent::class, function (TaskStartedEvent $event) use ($task) {
            return $event->task->id === $task->id;
        });
    });
});
