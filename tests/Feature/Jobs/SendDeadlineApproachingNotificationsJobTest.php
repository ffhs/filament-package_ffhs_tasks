<?php

use App\Models\User;
use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Actions\SendTaskNotification;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Jobs\SendDeadlineApproachingNotificationsJob;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskDeadlineApproachingNotification;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    config()->set('ffhs-tasks.notifications.enabled', [
        TaskDeadlineApproachingNotification::class,
    ]);

    config()->set('ffhs-tasks.notifications.deadline_remind_before', [
        CarbonInterval::days(7),
        CarbonInterval::days(3),
        CarbonInterval::days(1),
    ]);

    config()->set('ffhs-tasks.types', [TestTaskType::class]);
});

it('sends notification when deadline is within configured threshold', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->addDays(2),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendDeadlineApproachingNotificationsJob::class)->handle(
        app(SendTaskNotification::class)
    );

    Notification::assertSentTo($assignee, TaskDeadlineApproachingNotification::class);
});

it('does not send duplicate notifications for same threshold', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->addDays(2),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    $sender = app(SendTaskNotification::class);

    app(SendDeadlineApproachingNotificationsJob::class)->handle($sender);

    Notification::fake();

    app(SendDeadlineApproachingNotificationsJob::class)->handle($sender);

    Notification::assertNotSentTo($assignee, TaskDeadlineApproachingNotification::class);
});

it('does not send notification when deadline is far away', function () {
    Notification::fake();

    $assignee = User::factory()->create();
    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->addDays(30),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendDeadlineApproachingNotificationsJob::class)->handle(app(SendTaskNotification::class));

    Notification::assertNotSentTo($assignee, TaskDeadlineApproachingNotification::class);
});

it('does not send notifications when disabled', function () {
    config()->set('ffhs-tasks.notifications.enabled', []);

    Notification::fake();

    $assignee = User::factory()->create();
    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->addDays(2),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendDeadlineApproachingNotificationsJob::class)->handle(
        app(SendTaskNotification::class)
    );

    Notification::assertNothingSent();
});

it('only sends notifications for tasks with InProgress status', function (TaskStatus $status) {
    Notification::fake();

    $assignee = User::factory()->create();
    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->addDays(2),
    ]);
    $task->update(['status' => $status]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendDeadlineApproachingNotificationsJob::class)->handle(app(SendTaskNotification::class));

    Notification::assertNotSentTo($assignee, TaskDeadlineApproachingNotification::class);
})->with([
    'completed' => TaskStatus::Completed,
    'cancelled' => TaskStatus::Cancelled,
    'expired' => TaskStatus::Expired,
]);
