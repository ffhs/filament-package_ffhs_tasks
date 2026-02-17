<?php

use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Scopes\AssignablesScope;
use App\Models\SecondUserGroup;
use App\Models\FirstUserGroup;

test('filters tasks belonging to the authenticated user groups', function () {
    // Arrange
    config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

    $user = User::factory()->create();
    $group = FirstUserGroup::factory()->create();
    $group->users()->attach($user);

    $taskInGroup = Task::factory()->create();

    Assignable::query()->create([
        'task_id' => $taskInGroup->id,
        'assignable_type' => FirstUserGroup::class,
        'assignable_id' => $group->id,
    ]);

    $taskOutsideGroup = Task::factory()->create();

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new AssignablesScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($taskInGroup->id);
});

test('filters tasks across multiple user group types', function () {
    // Arrange
    config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class, SecondUserGroup::class]);

    $user = User::factory()->create();

    $firstGroup = FirstUserGroup::factory()->create();
    $firstGroup->users()->attach($user);

    $secondGroup = SecondUserGroup::factory()->create();
    $secondGroup->users()->attach($user);

    $taskA = Task::factory()->create();

    Assignable::query()->create([
        'task_id' => $taskA->id,
        'assignable_type' => FirstUserGroup::class,
        'assignable_id' => $firstGroup->id,
    ]);

    $taskB = Task::factory()->create();

    Assignable::query()->create([
        'task_id' => $taskB->id,
        'assignable_type' => SecondUserGroup::class,
        'assignable_id' => $secondGroup->id,
    ]);

    $taskC = Task::factory()->create();

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new AssignablesScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(2)
        ->and($results->pluck('id')->all())->toContain($taskA->id, $taskB->id);
});

test('returns empty collection when no tasks have user groups', function () {
    // Arrange
    config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

    $user = User::factory()->create();
    Task::factory()->create();

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new AssignablesScope())
        ->get();

    // Assert
    expect($results)->toBeEmpty();
});

test('does not return tasks from groups the user does not belong to', function () {
    // Arrange
    config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);

    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userGroup = FirstUserGroup::factory()->create();
    $userGroup->users()->attach($user);

    $otherGroup = FirstUserGroup::factory()->create();
    $otherGroup->users()->attach($otherUser);

    $userTask = Task::factory()->create();

    Assignable::query()->create([
        'task_id' => $userTask->id,
        'assignable_type' => FirstUserGroup::class,
        'assignable_id' => $userGroup->id,
    ]);

    $otherTask = Task::factory()->create();

    Assignable::query()->create([
        'task_id' => $otherTask->id,
        'assignable_type' => FirstUserGroup::class,
        'assignable_id' => $otherGroup->id,
    ]);

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new AssignablesScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($userTask->id);
});
