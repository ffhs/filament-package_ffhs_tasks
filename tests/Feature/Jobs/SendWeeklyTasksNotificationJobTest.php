<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Jobs\SendWeeklyTasksNotificationJob;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\WeeklyTasksNotification;
use Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes\TestTaskType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    config()->set('ffhs-tasks.notifications.enabled', [
        WeeklyTasksNotification::class,
    ]);

    config()->set('ffhs-tasks.types', [TestTaskType::class]);
});

it('sends notification for tasks with deadline in the current week', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->startOfWeek()->addDays(2),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendWeeklyTasksNotificationJob::class)->handle();

    Notification::assertSentTo($assignee, WeeklyTasksNotification::class);
});

it('groups multiple tasks per user into a single notification', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $tasks = Task::factory()
        ->count(3)
        ->create([
            'status' => TaskStatus::InProgress,
            'deadline_at' => Carbon::now()->startOfWeek()->addDays(2),
        ]);

    foreach ($tasks as $task) {
        $task->assignables()->create([
            'assignable_type' => User::class,
            'assignable_id' => $assignee->id,
        ]);
    }

    app(SendWeeklyTasksNotificationJob::class)->handle();

    Notification::assertSentToTimes($assignee, WeeklyTasksNotification::class, 1);

    Notification::assertSentTo($assignee, function (WeeklyTasksNotification $notification) {
        return $notification->tasks->count() === 3;
    });
});

it('does not send duplicate notifications for the same week', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->startOfWeek()->addDays(2),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendWeeklyTasksNotificationJob::class)->handle();

    Notification::fake();

    app(SendWeeklyTasksNotificationJob::class)->handle();

    Notification::assertNotSentTo($assignee, WeeklyTasksNotification::class);
});

it('does not send notification for tasks with deadline outside current week', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->addMonth(),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendWeeklyTasksNotificationJob::class)->handle();

    Notification::assertNotSentTo($assignee, WeeklyTasksNotification::class);
});

it('does not send notifications when disabled', function () {
    config()->set('ffhs-tasks.notifications.enabled', []);

    Notification::fake();

    $assignee = User::factory()->create();
    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->startOfWeek()->addDays(2),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendWeeklyTasksNotificationJob::class)->handle();

    Notification::assertNothingSent();
});

it('only sends notifications for tasks with InProgress status', function (TaskStatus $status) {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->startOfWeek()->addDays(2),
    ]);
    $task->update(['status' => $status]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendWeeklyTasksNotificationJob::class)->handle();

    Notification::assertNotSentTo($assignee, WeeklyTasksNotification::class);
})->with([
    'completed' => TaskStatus::Completed,
    'cancelled' => TaskStatus::Cancelled,
    'expired' => TaskStatus::Expired,
]);

it('does not send notification for tasks without deadline', function () {
    Notification::fake();

    $assignee = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => null,
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assignee->id,
    ]);

    app(SendWeeklyTasksNotificationJob::class)->handle();

    Notification::assertNotSentTo($assignee, WeeklyTasksNotification::class);
});

it('sends separate notifications to different assignees', function () {
    Notification::fake();

    $assigneeA = User::factory()->create();
    $assigneeB = User::factory()->create();

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
        'deadline_at' => Carbon::now()->startOfWeek()->addDays(2),
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assigneeA->id,
    ]);

    $task->assignables()->create([
        'assignable_type' => User::class,
        'assignable_id' => $assigneeB->id,
    ]);

    app(SendWeeklyTasksNotificationJob::class)->handle();

    Notification::assertSentTo($assigneeA, WeeklyTasksNotification::class);
    Notification::assertSentTo($assigneeB, WeeklyTasksNotification::class);
});
