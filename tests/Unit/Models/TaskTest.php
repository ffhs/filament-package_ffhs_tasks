<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\StatusChangedEvent;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Support\Facades\Event;

describe('cancel()', function () {
    it('sets status to Cancelled', function () {
        // Arrange
        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->cancel();

        // Assert
        expect($task->fresh())
            ->status->toBe(TaskStatus::Cancelled)
            ->cancelled_at->not->toBeNull();
    });

    it('dispatches StatusChangedEvent', function () {
        // Arrange
        Event::fake([StatusChangedEvent::class]);

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->cancel();

        // Assert
        Event::assertDispatched(StatusChangedEvent::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });
    });
});

describe('expire()', function () {
    it('sets status to Expired', function () {
        // Arrange
        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->expire();

        // Assert
        expect($task->fresh()->status)->toBe(TaskStatus::Expired);
    });

    it('dispatches StatusChangedEvent', function () {
        // Arrange
        Event::fake([StatusChangedEvent::class]);

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->expire();

        // Assert
        Event::assertDispatched(StatusChangedEvent::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });
    });
});

describe('complete()', function () {
    it('sets status to Completed', function () {
        // Arrange
        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->complete();

        // Assert
        expect($task->fresh())
            ->status->toBe(TaskStatus::Completed)
            ->completed_at->not->toBeNull();
    });

    it('dispatches StatusChangedEvent', function () {
        // Arrange
        Event::fake([StatusChangedEvent::class]);

        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->complete();

        // Assert
        Event::assertDispatched(StatusChangedEvent::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });
    });

    it('returns true on success', function () {
        // Arrange
        $task = Task::factory()->create([
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $result = $task->complete();

        // Assert
        expect($result)->toBeTrue();
    });
});
