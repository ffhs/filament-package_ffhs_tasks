<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Scopes\IsActiveScope;

test('IsActiveScope filters tasks with InProgress status', function () {
    // Arrange
    [$taskA, $taskB] = Task::factory()
        ->count(2)
        ->create([
            'status' => TaskStatus::InProgress,
        ]);

    Task::factory()
        ->completed()
        ->create();

    Task::factory()
        ->cancelled()
        ->create();

    Task::factory()
        ->expired()
        ->create();

    // Act
    $results = Task::query()
        ->tap(new IsActiveScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(2);
    expect($results->pluck('id'))->toContain($taskA->id, $taskB->id);
    expect($results->first()->status)->toBe(TaskStatus::InProgress);
});

test('IsActiveScope returns empty collection when no InProgress tasks exist', function () {
    // Arrange
    Task::factory()->cancelled()->create();
    Task::factory()->completed()->create();
    Task::factory()->expired()->create();

    // Act
    $results = Task::query()
        ->tap(new IsActiveScope())
        ->get();

    // Assert
    expect($results)->toBeEmpty();
});
