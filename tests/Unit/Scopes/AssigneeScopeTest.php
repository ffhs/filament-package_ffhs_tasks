<?php

use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Scopes\AssigneeScope;

test('filters tasks assigned to the given user', function () {
    // Arrange
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $assignedTask = Task::factory()->create();
    $assignedTask->users()->attach($user);

    $unassignedTask = Task::factory()->create();
    $unassignedTask->users()->attach($otherUser);

    $this->actingAs($otherUser);

    // Act
    $results = Task::query()
        ->tap(new AssigneeScope($user))
        ->get();

    // Assert
    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($assignedTask->id);
});

test('defaults to the authenticated user', function () {
    // Arrange
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $assignedTask = Task::factory()->create();
    $assignedTask->users()->attach($user);

    $unassignedTask = Task::factory()->create();
    $unassignedTask->users()->attach($otherUser);

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new AssigneeScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($assignedTask->id);
});

test('returns empty collection when user has no assigned tasks', function () {
    // Arrange
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $task = Task::factory()->create();
    $task->users()->attach($otherUser);

    // Act
    $results = Task::query()
        ->tap(new AssigneeScope($user))
        ->get();

    // Assert
    expect($results)->toBeEmpty();
});

test('returns multiple tasks assigned to the same user', function () {
    // Arrange
    $user = User::factory()->create();

    $tasks = Task::factory()
        ->count(3)
        ->create();

    $tasks->each(fn (Task $task) => $task->users()->attach($user));

    // Act
    $results = Task::query()
        ->tap(new AssigneeScope($user))
        ->get();

    // Assert
    expect($results)->toHaveCount(3);
});
