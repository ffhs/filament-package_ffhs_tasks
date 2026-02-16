<?php

use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Scopes\CreatorScope;

test('filters tasks created by the given user', function () {
    // Arrange
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $createdTask = Task::factory()->create([
        'creator_type' => $user::class,
        'creator_id' => $user->id,
    ]);

    $otherTask = Task::factory()->create([
        'creator_type' => $otherUser::class,
        'creator_id' => $otherUser->id,
    ]);

    $this->actingAs($otherUser);

    // Act
    $results = Task::query()
        ->tap(new CreatorScope($user))
        ->get();

    // Assert
    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($createdTask->id);
});

test('defaults to the authenticated user', function () {
    // Arrange
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $createdTask = Task::factory()->create([
        'creator_type' => $user::class,
        'creator_id' => $user->id,
    ]);

    Task::factory()->create([
        'creator_type' => $otherUser::class,
        'creator_id' => $otherUser->id,
    ]);

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new CreatorScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($createdTask->id);
});

test('returns empty collection when user has not created any tasks', function () {
    // Arrange
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Task::factory()->create([
        'creator_type' => $otherUser::class,
        'creator_id' => $otherUser->id,
    ]);

    // Act
    $results = Task::query()
        ->tap(new CreatorScope($user))
        ->get();

    // Assert
    expect($results)->toBeEmpty();
});

test('returns multiple tasks created by the same user', function () {
    // Arrange
    $user = User::factory()->create();

    Task::factory()
        ->count(3)
        ->create([
            'creator_type' => $user::class,
            'creator_id' => $user->id,
        ]);

    // Act
    $results = Task::query()
        ->tap(new CreatorScope($user))
        ->get();

    // Assert
    expect($results)->toHaveCount(3);
});
