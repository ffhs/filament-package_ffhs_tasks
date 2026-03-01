<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\StatusChangedEvent;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskTag;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use App\Models\FirstUserGroup;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    config()->set('ffhs-tasks.types', [
        TestTaskType::class,
    ]);

    TestTaskType::resetFlags();
});

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

    it('calls mutateDataBeforeCancel lifecycle hook', function () {
        // Arrange
        $task = Task::factory()->create([
            'type' => TestTaskType::identifier(),
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->cancel();

        // Assert
        expect(TestTaskType::$mutateDataBeforeCancelCalled)->toBeTrue();
    });

    it('calls afterCancel lifecycle hook', function () {
        // Arrange
        $task = Task::factory()->create([
            'type' => TestTaskType::identifier(),
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->cancel();

        // Assert
        expect(TestTaskType::$afterCancelCalled)->toBeTrue();
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

    it('calls mutateDataBeforeExpire lifecycle hook', function () {
        // Arrange
        $task = Task::factory()->create([
            'type' => TestTaskType::identifier(),
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->expire();

        // Assert
        expect(TestTaskType::$mutateDataBeforeExpireCalled)->toBeTrue();
    });

    it('calls afterExpire lifecycle hook', function () {
        // Arrange
        $task = Task::factory()->create([
            'type' => TestTaskType::identifier(),
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->expire();

        // Assert
        expect(TestTaskType::$afterExpireCalled)->toBeTrue();
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

    it('calls mutateDataBeforeComplete lifecycle hook', function () {
        // Arrange
        $task = Task::factory()->create([
            'type' => TestTaskType::identifier(),
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->complete();

        // Assert
        expect(TestTaskType::$mutateDataBeforeCompleteCalled)->toBeTrue();
    });

    it('calls afterComplete lifecycle hook', function () {
        // Arrange
        $task = Task::factory()->create([
            'type' => TestTaskType::identifier(),
            'status' => TaskStatus::InProgress,
        ]);

        // Act
        $task->complete();

        // Assert
        expect(TestTaskType::$afterCompleteCalled)->toBeTrue();
    });
});

describe('tags()', function () {
    it('returns associated tags', function () {
        // Arrange
        $task = Task::factory()->create();
        $tags = TaskTag::factory()->count(2)->create();
        $task->tags()->attach($tags);

        // Act
        $result = $task->tags;

        // Assert
        expect($result)->toHaveCount(2);
    });
});

describe('creator()', function () {
    it('allows a non-user model as creator', function () {
        // Arrange
        $creator = FirstUserGroup::factory()->create();

        // Act
        $task = Task::factory()->create([
            'creator_type' => $creator->getMorphClass(),
            'creator_id' => $creator->getKey(),
        ]);

        // Assert
        expect($task->fresh()->creator)
            ->toBeInstanceOf(FirstUserGroup::class)
            ->getKey()->toBe($creator->getKey());
    });
});
