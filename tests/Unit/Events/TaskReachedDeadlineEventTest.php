<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\TaskReachedDeadlineEvent;
use Ffhs\FfhsTasks\Jobs\DispatchTaskReachedDeadlineEventJob;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    config()->set('ffhs-tasks.types', [TestTaskType::class]);
});

describe('TaskReachedDeadlineEvent', function () {
    it('is dispatched when a task reaches its deadline', function () {
        // Arrange
        Event::fake([TaskReachedDeadlineEvent::class]);

        Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now(),
        ]);

        // Act
        (new DispatchTaskReachedDeadlineEventJob())->handle();

        // Assert
        Event::assertDispatched(TaskReachedDeadlineEvent::class);
    });

    it('is not dispatched when deadline is in the future', function () {
        // Arrange
        Event::fake([TaskReachedDeadlineEvent::class]);

        Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->addMinute(),
        ]);

        // Act
        (new DispatchTaskReachedDeadlineEventJob())->handle();

        // Assert
        Event::assertNotDispatched(TaskReachedDeadlineEvent::class);
    });

    it('is not dispatched twice for the same task', function () {
        // Arrange
        Event::fake([TaskReachedDeadlineEvent::class]);

        Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subMinute(),
        ]);

        // Act
        (new DispatchTaskReachedDeadlineEventJob())->handle();
        (new DispatchTaskReachedDeadlineEventJob())->handle();

        // Assert
        Event::assertDispatchedTimes(TaskReachedDeadlineEvent::class, 1);
    });

    it('is not dispatched for non-InProgress tasks', function (TaskStatus $status) {
        // Arrange
        Event::fake([TaskReachedDeadlineEvent::class]);

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subMinute(),
        ]);
        $task->update(['status' => $status]);

        // Act
        (new DispatchTaskReachedDeadlineEventJob())->handle();

        // Assert
        Event::assertNotDispatched(TaskReachedDeadlineEvent::class);
    })->with([
        'completed' => TaskStatus::Completed,
        'cancelled' => TaskStatus::Cancelled,
        'expired' => TaskStatus::Expired,
    ]);

    it('carries the task model', function () {
        // Arrange
        Event::fake([TaskReachedDeadlineEvent::class]);

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subMinute(),
        ]);

        // Act
        (new DispatchTaskReachedDeadlineEventJob())->handle();

        // Assert
        Event::assertDispatched(TaskReachedDeadlineEvent::class, function (TaskReachedDeadlineEvent $event) use ($task) {
            return $event->task->id === $task->id;
        });
    });
});
