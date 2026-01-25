<?php

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Scopes\IsArchivedScope;

test('IsArchivedScope filters tasks that are not InProgress', function () {
    // Arrange
    $completedTask = Task::factory()->completed()->create();
    $cancelledTask = Task::factory()->cancelled()->create();
    $expiredTask = Task::factory()->expired()->create();

    Task::factory()
        ->count(2)
        ->create([
            'status' => TaskStatus::InProgress,
        ]);

    // Act
    $results = Task::query()
        ->tap(new IsArchivedScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(3);
    expect($results->pluck('id'))->toContain($completedTask->id, $cancelledTask->id, $expiredTask->id);
    expect($results->pluck('status'))->not->toContain(TaskStatus::InProgress);
});

test('IsArchivedScope returns empty collection when only InProgress tasks exist', function () {
    // Arrange
    Task::factory()
        ->count(3)
        ->create([
            'status' => TaskStatus::InProgress,
        ]);

    // Act
    $results = Task::query()
        ->tap(new IsArchivedScope())
        ->get();

    // Assert
    expect($results)->toBeEmpty();
});
