<?php

use App\Models\User;
use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Actions\SendTaskNotification;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Jobs\SendDeadlineExceededNotificationsJob;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskDeadlineExceededNotification;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    config()->set('ffhs-tasks.notifications.enabled', [
        TaskDeadlineExceededNotification::class,
    ]);

    config()->set('ffhs-tasks.notifications.deadline_remind_after', [
        CarbonInterval::hours(0),
        CarbonInterval::days(3),
        CarbonInterval::days(7),
    ]);

    config()->set('ffhs-tasks.types', [TestTaskType::class]);
});

it('sends notification when deadline has passed', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->subHour(),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendDeadlineExceededNotificationsJob::class)->handle(
        app(SendTaskNotification::class)
    );

    Notification::assertSentTo($assignee, TaskDeadlineExceededNotification::class);
});

it('does not send duplicate notifications for same threshold', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->subHour(),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    $sender = app(SendTaskNotification::class);

    app(SendDeadlineExceededNotificationsJob::class)->handle($sender);

    Notification::fake();

    app(SendDeadlineExceededNotificationsJob::class)->handle($sender);

    Notification::assertNotSentTo($assignee, TaskDeadlineExceededNotification::class);
});

it('does not send notifications when disabled', function () {
    config()->set('ffhs-tasks.notifications.enabled', []);

    Notification::fake();

    $assignee = User::factory()->create();
    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->subHour(),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendDeadlineExceededNotificationsJob::class)->handle(app(SendTaskNotification::class));

    Notification::assertNothingSent();
});

it('only sends notifications for tasks with InProgress status', function (TaskStatus $status) {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->subHour(),
    ]);
    $task->update(['status' => $status]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendDeadlineExceededNotificationsJob::class)->handle(app(SendTaskNotification::class));

    Notification::assertNotSentTo($assignee, TaskDeadlineExceededNotification::class);
})->with([
    'completed' => TaskStatus::Completed,
    'cancelled' => TaskStatus::Cancelled,
    'expired' => TaskStatus::Expired,
]);
