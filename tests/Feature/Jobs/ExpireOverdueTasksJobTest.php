<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\StatusChangedEvent;
use Ffhs\FfhsTasks\Jobs\ExpireOverdueTasksJob;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\ExpirableTaskType;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\NonExpirableTaskType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    config()->set('ffhs-tasks.types', [
        ExpirableTaskType::class,
        NonExpirableTaskType::class,
    ]);

    ExpirableTaskType::resetFlags();
});

describe('ExpireOverdueTasksJob', function () {
    it('expires tasks that are past their deadline when taskType allows expiration', function () {
        // Arrange
        $overdueTask = Task::factory()->create([
            'type' => 'expirable',
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect($overdueTask->fresh()->status)->toBe(TaskStatus::Expired);
    });

    it('does not expire tasks when taskType does not allow expiration', function () {
        // Arrange
        $overdueTask = Task::factory()->create([
            'type' => 'non-expirable',
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect($overdueTask->fresh()->status)->toBe(TaskStatus::InProgress);
    });

    it('does not expire tasks that are not past their deadline', function () {
        // Arrange
        $futureTask = Task::factory()->create([
            'type' => 'expirable',
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
            'type' => 'expirable',
            'deadline_at' => Carbon::now()->subDay(),
        ]);
        $completedTask->update(['status' => TaskStatus::Completed]);

        $cancelledTask = Task::factory()->create([
            'type' => 'expirable',
            'deadline_at' => Carbon::now()->subDay(),
        ]);
        $cancelledTask->update(['status' => TaskStatus::Cancelled]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect($completedTask->fresh()->status)->toBe(TaskStatus::Completed);
        expect($cancelledTask->fresh()->status)->toBe(TaskStatus::Cancelled);
    });

    it('dispatches StatusChangedEvent only for tasks that are expired', function () {
        // Arrange
        Event::fake([StatusChangedEvent::class]);

        Task::factory()
            ->count(2)
            ->create([
                'type' => 'expirable',
                'status' => TaskStatus::InProgress,
                'deadline_at' => Carbon::now()->subDay(),
            ]);

        Task::factory()->create([
            'type' => 'non-expirable',
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        Event::assertDispatchedTimes(StatusChangedEvent::class, 2);
    });

    it('does not dispatch StatusChangedEvent for tasks not expiring', function () {
        // Arrange
        Event::fake([StatusChangedEvent::class]);

        Task::factory()->create([
            'type' => 'expirable',
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->addDay(),
        ]);

        Task::factory()->create([
            'type' => 'non-expirable',
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        Event::assertNotDispatched(StatusChangedEvent::class);
    });

    it('does not expire tasks without a deadline', function () {
        // Arrange
        $taskWithoutDeadline = Task::factory()->create([
            'type' => 'expirable',
            'status' => TaskStatus::InProgress,
            'deadline_at' => null,
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect($taskWithoutDeadline->fresh()->status)->toBe(TaskStatus::InProgress);
    });

    it('calls mutateDataBeforeExpire lifecycle hook', function () {
        // Arrange
        Task::factory()->create([
            'type' => ExpirableTaskType::identifier(),
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect(ExpirableTaskType::$mutateDataBeforeExpireCalled)->toBeTrue();
    });

    it('calls afterExpire lifecycle hook', function () {
        // Arrange
        Task::factory()->create([
            'type' => ExpirableTaskType::identifier(),
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->subDay(),
        ]);

        // Act
        (new ExpireOverdueTasksJob())->handle();

        // Assert
        expect(ExpirableTaskType::$afterExpireCalled)->toBeTrue();
    });
});
