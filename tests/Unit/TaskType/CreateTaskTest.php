<?php

use Ffhs\FfhsTasks\Enums\TaskPrivacy;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Exceptions\TaskCreateDataException;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\CreateTaskType;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Validation\ValidationException;

describe('createTask', function () {
    it('creates a task with valid data', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [CreateTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);

        $taskType = new CreateTaskType();

        // Act
        $task = $taskType->createTask([
            'title' => 'My Task',
            'description' => 'A description',
            'privacy' => TaskPrivacy::Public->value,
            'can_be_cancelled' => true,
            'extra' => [
                'reason' => 'Important reason',
            ],
        ]);

        // Assert
        expect($task)
            ->toBeInstanceOf(Task::class)
            ->title->toBe('My Task')
            ->description->toBe('A description')
            ->privacy->toBe(TaskPrivacy::Public->value);
    });

    it('stores extra data from main components', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [CreateTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);

        $taskType = new CreateTaskType();

        // Act
        $task = $taskType->createTask([
            'title' => 'My Task',
            'description' => 'A description',
            'privacy' => TaskPrivacy::Public->value,
            'can_be_cancelled' => false,
            'extra' => [
                'reason' => 'Budget review needed',
            ],
        ]);

        // Assert
        expect($task->extra)
            ->toHaveKey('reason', 'Budget review needed');
    });

    it('stores extra data from sidebar components', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [CreateTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);

        $taskType = new CreateTaskType();

        // Act
        $task = $taskType->createTask([
            'title' => 'My Task',
            'description' => 'A description',
            'privacy' => TaskPrivacy::Public->value,
            'can_be_cancelled' => false,
            'extra' => [
                'reason' => 'A reason',
                'is_urgent' => true,
            ],
        ]);

        // Assert
        expect($task->extra)
            ->toHaveKey('reason', 'A reason')
            ->toHaveKey('is_urgent', true);
    });

    it('creates a task without type-specific extra fields when type has no components', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [TestTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);

        $taskType = new TestTaskType();

        // Act
        $task = $taskType->createTask([
            'title' => 'Simple Task',
            'description' => 'A simple description',
            'privacy' => TaskPrivacy::Private->value,
            'can_be_cancelled' => true,
        ]);

        // Assert
        expect($task)
            ->toBeInstanceOf(Task::class)
            ->title->toBe('Simple Task')
            ->privacy->toBe(TaskPrivacy::Private->value);
    });

    it('accepts optional starts_at and deadline_at dates', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [CreateTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);

        $taskType = new CreateTaskType();

        // Act
        $task = $taskType->createTask([
            'title' => 'Scheduled Task',
            'description' => 'A description',
            'privacy' => TaskPrivacy::Public->value,
            'can_be_cancelled' => false,
            'starts_at' => \Illuminate\Support\Facades\Date::parse('2026-03-01 10:00:00'),
            'deadline_at' => \Illuminate\Support\Facades\Date::parse('2026-03-15 18:00:00'),
            'extra' => [
                'reason' => 'A reason',
            ],
        ]);

        // Assert
        expect($task)
            ->starts_at->not->toBeNull()
            ->deadline_at->not->toBeNull();
    });
});

describe('createTask validation', function () {
    it('fails when field is missing', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [CreateTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);

        $taskType = new CreateTaskType();

        // Act & Assert
        $taskType->createTask([
            'description' => 'A description',
            'privacy' => TaskPrivacy::Public->value,
            'can_be_cancelled' => false,
            'extra' => ['reason' => 'A reason'],
        ]);
    })->throws(TaskCreateDataException::class);

    it('returns validation errors for the correct fields', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [CreateTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);

        $taskType = new CreateTaskType();

        // Act & Assert
        try {
            $taskType->createTask([]);
        } catch (TaskCreateDataException $e) {
            expect($e->getPrevious())
                ->toBeInstanceOf(ValidationException::class)
                ->and($e->getPrevious()->errors())
                ->toHaveKey('title')
                ->toHaveKey('description');

            return;
        }

        $this->fail('ValidationException was not thrown.');
    });


    it('backfills defaults', function () {
        // Arrange
        config()->set('ffhs-tasks.types', [CreateTaskType::class]);
        config()->set('ffhs-tasks.assignable_models', []);

        $taskType = new CreateTaskType();

        // Act & Assert
        $task = $taskType->createTask([
            'title' => 'Title',
            'description' => 'Description',
        ]);

        expect($task)
            ->status->toBe(TaskStatus::InProgress)
            ->privacy->toBe(TaskPrivacy::Public);
    });
});
