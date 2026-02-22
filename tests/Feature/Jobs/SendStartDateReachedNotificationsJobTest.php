<?php

use App\Models\User;
use Ffhs\FfhsTasks\Actions\SendTaskNotification;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Jobs\SendStartDateReachedNotificationsJob;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskStartDateReachedNotification;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    config()->set('ffhs-tasks.notifications.enabled', [
        TaskStartDateReachedNotification::class,
    ]);

    config()->set('ffhs-tasks.types', [TestTaskType::class]);
});

it('sends notification when start date is reached', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'starts_at' => Carbon::now()->subMinute(),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendStartDateReachedNotificationsJob::class)->handle(
        app(SendTaskNotification::class)
    );

    Notification::assertSentTo($assignee, TaskStartDateReachedNotification::class);
});

it('does not send notification when start date is in the future', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'starts_at' => Carbon::now()->addDay(),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendStartDateReachedNotificationsJob::class)->handle(
        app(SendTaskNotification::class)
    );

    Notification::assertNotSentTo($assignee, TaskStartDateReachedNotification::class);
});

it('does not send duplicate start date notifications', function () {
    Notification::fake();

    $assignee = User::factory()->create();
    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'starts_at' => Carbon::now()->subMinute(),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    $sender = app(SendTaskNotification::class);

    app(SendStartDateReachedNotificationsJob::class)->handle($sender);

    Notification::fake();

    app(SendStartDateReachedNotificationsJob::class)->handle($sender);

    Notification::assertNotSentTo($assignee, TaskStartDateReachedNotification::class);
});

it('does not send notifications when disabled', function () {
    config()->set('ffhs-tasks.notifications.enabled', []);

    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'starts_at' => Carbon::now()->subMinute(),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendStartDateReachedNotificationsJob::class)->handle(
        app(SendTaskNotification::class)
    );

    Notification::assertNothingSent();
});

it('only sends notifications for tasks with InProgress status', function (TaskStatus $status) {
    Notification::fake();

    $assignee = User::factory()->create();
    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'starts_at' => Carbon::now()->subMinute(),
    ]);
    $task->update(['status' => $status]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendStartDateReachedNotificationsJob::class)->handle(
        app(SendTaskNotification::class)
    );

    Notification::assertNotSentTo($assignee, TaskStartDateReachedNotification::class);
})->with([
    'completed' => TaskStatus::Completed,
    'cancelled' => TaskStatus::Cancelled,
    'expired' => TaskStatus::Expired,
]);
