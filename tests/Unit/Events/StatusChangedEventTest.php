<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\StatusChangedEvent;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Support\Facades\Event;

test('StatusChangedEvent is fired when task status is updated', function () {
    // Arrange
    Event::fake([StatusChangedEvent::class]);

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
    ]);

    // Act
    $task->update([
        'status' => TaskStatus::Completed,
    ]);

    // Assert
    Event::assertDispatched(StatusChangedEvent::class, function ($event) use ($task) {
        return $event->task->id === $task->id;
    });
});

test('StatusChangedEvent is not fired when task is updated without status change', function () {
    // Arrange
    Event::fake([StatusChangedEvent::class]);

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'title' => 'Original Title',
    ]);

    // Act
    $task->update([
        'title' => 'Updated Title',
    ]);

    // Assert
    Event::assertNotDispatched(StatusChangedEvent::class);
});
