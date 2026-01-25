<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\StatusChangedEvent;
use Ffhs\FfhsTasks\Jobs\ExpireOverdueTasksJob;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

describe('ExpireOverdueTasksJob', function () {
    it('expires tasks that are past their deadline', function () {
        // Arrange
        $overdueTask = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect($overdueTask->fresh()->status)->toBe(TaskStatus::Expired);
    });

    it('does not expire tasks that are not past their deadline', function () {
        // Arrange
        $futureTask = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->addDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect($futureTask->fresh()->status)->toBe(TaskStatus::InProgress);
    });

    it('only expires tasks with InProgress status', function () {
        // Arrange
        $completedTask = Task::factory()->create([
            'status' => TaskStatus::Completed,
            'deadline_at' => Carbon::now()->subDay(),
        ]);

        $cancelledTask = Task::factory()->create([
            'status' => TaskStatus::Cancelled,
            'deadline_at' => Carbon::now()->subDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect($completedTask->fresh()->status)->toBe(TaskStatus::Completed);
        expect($cancelledTask->fresh()->status)->toBe(TaskStatus::Cancelled);
    });

    it('dispatches StatusChangedEvent for each expired task', function () {
        // Arrange
        Event::fake([StatusChangedEvent::class]);

        $overdueTasks = Task::factory()
            ->count(3)
            ->create([
                'status' => TaskStatus::InProgress,
                'deadline_at' => Carbon::now()->subDay(),
            ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        Event::assertDispatchedTimes(StatusChangedEvent::class, 3);
    });

    it('does not dispatch StatusChangedEvent for tasks not expiring', function () {
        // Arrange
        Event::fake([StatusChangedEvent::class]);

        Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->addDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        Event::assertNotDispatched(StatusChangedEvent::class);
    });

    it('does not expire tasks without a deadline', function () {
        // Arrange
        $taskWithoutDeadline = Task::factory()->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => null,
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect($taskWithoutDeadline->fresh()->status)->toBe(TaskStatus::InProgress);
    });
});
