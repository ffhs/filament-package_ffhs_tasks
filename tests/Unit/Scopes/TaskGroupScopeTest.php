<?php

use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskUserGroup;
use Ffhs\FfhsTasks\Scopes\TaskUserGroupScope;
use Ffhs\FfhsTasks\Tests\Fixtures\UserGroups\AnotherTestUserGroup;
use Ffhs\FfhsTasks\Tests\Fixtures\UserGroups\TestUserGroup;

test('filters tasks belonging to the authenticated user groups', function () {
    // Arrange
    config()->set('ffhs-tasks.user_groups', [TestUserGroup::class]);

    $user = User::factory()->create();
    $group = TestUserGroup::factory()->create();
    $group->users()->attach($user);

    $taskInGroup = Task::factory()->create();

    TaskUserGroup::query()->create([
        'task_id' => $taskInGroup->id,
        'user_group_type' => TestUserGroup::class,
        'user_group_id' => $group->id,
    ]);

    $taskOutsideGroup = Task::factory()->create();

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new TaskUserGroupScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($taskInGroup->id);
});

test('filters tasks across multiple user group types', function () {
    // Arrange
    config()->set('ffhs-tasks.user_groups', [TestUserGroup::class, AnotherTestUserGroup::class]);

    $user = User::factory()->create();

    $firstGroup = TestUserGroup::factory()->create();
    $firstGroup->users()->attach($user);

    $secondGroup = AnotherTestUserGroup::factory()->create();
    $secondGroup->users()->attach($user);

    $taskA = Task::factory()->create();

    TaskUserGroup::query()->create([
        'task_id' => $taskA->id,
        'user_group_type' => TestUserGroup::class,
        'user_group_id' => $firstGroup->id,
    ]);

    $taskB = Task::factory()->create();

    TaskUserGroup::query()->create([
        'task_id' => $taskB->id,
        'user_group_type' => AnotherTestUserGroup::class,
        'user_group_id' => $secondGroup->id,
    ]);

    $taskC = Task::factory()->create();

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new TaskUserGroupScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(2)
        ->and($results->pluck('id')->all())->toContain($taskA->id, $taskB->id);
});

test('returns empty collection when no tasks have user groups', function () {
    // Arrange
    config()->set('ffhs-tasks.user_groups', [TestUserGroup::class]);

    $user = User::factory()->create();
    Task::factory()->create();

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new TaskUserGroupScope())
        ->get();

    // Assert
    expect($results)->toBeEmpty();
});

test('does not return tasks from groups the user does not belong to', function () {
    // Arrange
    config()->set('ffhs-tasks.user_groups', [TestUserGroup::class]);

    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userGroup = TestUserGroup::factory()->create();
    $userGroup->users()->attach($user);

    $otherGroup = TestUserGroup::factory()->create();
    $otherGroup->users()->attach($otherUser);

    $userTask = Task::factory()->create();

    TaskUserGroup::query()->create([
        'task_id' => $userTask->id,
        'user_group_type' => TestUserGroup::class,
        'user_group_id' => $userGroup->id,
    ]);

    $otherTask = Task::factory()->create();

    TaskUserGroup::query()->create([
        'task_id' => $otherTask->id,
        'user_group_type' => TestUserGroup::class,
        'user_group_id' => $otherGroup->id,
    ]);

    $this->actingAs($user);

    // Act
    $results = Task::query()
        ->tap(new TaskUserGroupScope())
        ->get();

    // Assert
    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($userTask->id);
});
