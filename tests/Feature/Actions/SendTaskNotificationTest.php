<?php

use App\Models\FirstUserGroup;
use App\Models\User;
use Ffhs\FfhsTasks\Actions\SendTaskNotification;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskAssignedNotification;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    config()->set('ffhs-tasks.notifications.enabled', [TaskAssignedNotification::class]);
    config()->set('ffhs-tasks.types', [TestTaskType::class]);
    config()->set('ffhs-tasks.assignable_models', [FirstUserGroup::class]);
});

describe('SendTaskNotification', function () {
    it('sends notification to user assignees', function () {
        // Arrange
        Notification::fake();

        $assignee = User::factory()->create();

        $task = Task::factory()->create(['status' => TaskStatus::InProgress]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assignee->id,
        ]);

        $notification = new TaskAssignedNotification($task);

        // Act
        app(SendTaskNotification::class)->execute($task, $notification);

        // Assert
        Notification::assertSentTo($assignee, TaskAssignedNotification::class);
    });

    it('excludes the actor from notifications', function () {
        // Arrange
        Notification::fake();

        $actor = User::factory()->create();
        $assignee = User::factory()->create();
        $task = Task::factory()->create(['status' => TaskStatus::InProgress]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $actor->id,
        ]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assignee->id,
        ]);

        $notification = new TaskAssignedNotification($task);

        // Act
        app(SendTaskNotification::class)->execute($task, $notification, $actor);

        // Assert
        Notification::assertSentTo($assignee, TaskAssignedNotification::class);
        Notification::assertNotSentTo($actor, TaskAssignedNotification::class);
    });

    it('sends notification directly to group when group is notifiable with a mail route', function () {
        // Arrange
        Notification::fake();

        $user = User::factory()->create();

        $group = FirstUserGroup::factory()->create();
        $group->email = 'group@example.com';
        $group->users()->attach($user);

        $task = Task::factory()->create(['status' => TaskStatus::InProgress]);
        $notification = new TaskAssignedNotification($task);

        // Act
        app(SendTaskNotification::class)->notifyModel($group, $notification);

        // Assert
        Notification::assertSentTo($group, TaskAssignedNotification::class);
        Notification::assertNotSentTo($user, TaskAssignedNotification::class);
    });

    it('sends notification to group members when group has no mail route', function () {
        // Arrange
        Notification::fake();

        $user = User::factory()->create();

        $group = FirstUserGroup::factory()->create();
        $group->users()->attach($user);

        $task = Task::factory()->create(['status' => TaskStatus::InProgress]);

        $task->assignables()->create([
            'assignable_type' => FirstUserGroup::class,
            'assignable_id' => $group->id,
        ]);

        $notification = new TaskAssignedNotification($task);

        // Act
        app(SendTaskNotification::class)->execute($task, $notification);

        // Assert
        Notification::assertSentTo($user, TaskAssignedNotification::class);
    });

    it('sends notification to multiple assignees', function () {
        // Arrange
        Notification::fake();

        $assigneeA = User::factory()->create();
        $assigneeB = User::factory()->create();

        $task = Task::factory()->create(['status' => TaskStatus::InProgress]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assigneeA->id,
        ]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assigneeB->id,
        ]);

        $notification = new TaskAssignedNotification($task);

        // Act
        app(SendTaskNotification::class)->execute($task, $notification);

        // Assert
        Notification::assertSentTo($assigneeA, TaskAssignedNotification::class);
        Notification::assertSentTo($assigneeB, TaskAssignedNotification::class);
    });

    it('sends notification to watchables', function () {
        // Arrange
        Notification::fake();

        $watcher = User::factory()->create();
        $task = Task::factory()->create(['status' => TaskStatus::InProgress]);

        $task->watchables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $watcher->id,
        ]);

        $notification = new TaskAssignedNotification($task);

        // Act
        app(SendTaskNotification::class)->execute($task, $notification);

        // Assert
        Notification::assertSentTo($watcher, TaskAssignedNotification::class);
    });

    it('does not send duplicate notifications when user is both assignee and watcher', function () {
        // Arrange
        Notification::fake();

        $user = User::factory()->create();
        $task = Task::factory()->create(['status' => TaskStatus::InProgress]);

        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $user->id,
        ]);

        $task->watchables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $user->id,
        ]);

        $notification = new TaskAssignedNotification($task);

        // Act
        app(SendTaskNotification::class)->execute($task, $notification);

        // Assert
        Notification::assertSentToTimes($user, TaskAssignedNotification::class, 1);
    });

    it('excludes the actor from watchable notifications', function () {
        // Arrange
        Notification::fake();

        $actor = User::factory()->create();
        $watcher = User::factory()->create();
        $task = Task::factory()->create(['status' => TaskStatus::InProgress]);

        $task->watchables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $actor->id,
        ]);

        $task->watchables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $watcher->id,
        ]);

        $notification = new TaskAssignedNotification($task);

        // Act
        app(SendTaskNotification::class)->execute($task, $notification, $actor);

        // Assert
        Notification::assertSentTo($watcher, TaskAssignedNotification::class);
        Notification::assertNotSentTo($actor, TaskAssignedNotification::class);
    });
});
