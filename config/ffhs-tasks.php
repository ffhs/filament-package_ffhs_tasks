<?php

use App\Models\User;
use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Models\NotificationLog;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\Watchable;
use Ffhs\FfhsTasks\Notifications;
use Ffhs\FfhsTasks\TaskType\DefaultTaskType;

return [

    /*
    |--------------------------------------------------------------------------
    | Run Migrations
    |--------------------------------------------------------------------------
    |
    | Set to false to disable automatic migrations. You will need to publish
    | and manage the migrations yourself.
    |
    */

    'run_migrations' => true,

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Only notification classes listed in the 'enabled' array will be sent.
    | An empty array disables all notifications.
    |
    | 'deadline_remind_before' and 'deadline_remind_after' define at which
    | intervals notifications are sent relative to the deadline. These can
    | be overridden per TaskType via deadlineRemindBefore() / deadlineRemindAfter().
    |
    | 'weekly_tasks.time' sets the time (HH:MM) for the weekly digest email,
    | sent every Monday.
    |
    */

    'notifications' => [
        'enabled' => [
            Notifications\TaskAssignedNotification::class,
            Notifications\TaskStatusChangedNotification::class,
            Notifications\TaskStartDateReachedNotification::class,
            Notifications\TaskDeadlineApproachingNotification::class,
            Notifications\TaskDeadlineExceededNotification::class,
            Notifications\WeeklyTasksNotification::class,
        ],
        'deadline_remind_before' => [CarbonInterval::days(7), CarbonInterval::days(3), CarbonInterval::days(1)],
        'deadline_remind_after' => [CarbonInterval::hours(0), CarbonInterval::days(3), CarbonInterval::days(7)],
        'weekly_tasks' => [
            'time' => '08:00',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | Customize the database table names used by the package.
    |
    */

    'tables' => [
        'tasks' => 'ffhs_tasks',
        'task_assignables' => 'ffhs_task_assignables',
        'task_watchables' => 'ffhs_task_watchables',
        'task_notification_log' => 'ffhs_task_notification_log',
        'task_tag' => 'ffhs_task_tag',
        'task_tags' => 'ffhs_task_tags',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Overrides
    |--------------------------------------------------------------------------
    |
    | Swap internal models with your own subclasses. Map the original class
    | to your custom class (e.g. Task::class => \App\Models\CustomTask::class).
    |
    */

    'models' => [
        Task::class => Task::class,
        NotificationLog::class => NotificationLog::class,
        Assignable::class => Assignable::class,
        Watchable::class => Watchable::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    |
    | The user model class and the attribute used to display the user's name.
    |
    */

    'user' => [
        'model' => User::class,
        'name_attribute' => 'name',
    ],

    /*
    |--------------------------------------------------------------------------
    | Assignable Models
    |--------------------------------------------------------------------------
    |
    | Models that can be assigned to tasks. The User model is always supported.
    | Additional models must implement AssignableInterface.
    |
    */

    'assignable_models' => [
        User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Task Types
    |--------------------------------------------------------------------------
    |
    | Registered task type classes. Each must extend TaskType and define
    | a unique static $identifier.
    |
    */

    'types' => [
        DefaultTaskType::class,
    ],
];
