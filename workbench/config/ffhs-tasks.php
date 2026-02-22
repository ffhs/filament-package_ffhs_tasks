<?php

use App\Models\FirstUserGroup;
use App\Models\SecondUserGroup;
use App\Models\User;
use App\TaskTypes\ApprovalTaskType;
use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Models\NotificationLog;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications;
use Ffhs\FfhsTasks\TaskType\DefaultTaskType;

return [
    'run_migrations' => true,

    'notifications' => [
        'enabled' => [
            Notifications\TaskAssignedNotification::class,
            Notifications\TaskStatusChangedNotification::class,
            Notifications\TaskStartDateReachedNotification::class,
            Notifications\TaskDeadlineApproachingNotification::class,
            Notifications\TaskDeadlineExceededNotification::class,
        ],
        'deadline_remind_before' => [CarbonInterval::days(7), CarbonInterval::days(3), CarbonInterval::days(1)],
        'deadline_remind_after' => [CarbonInterval::hours(0), CarbonInterval::days(3), CarbonInterval::days(7)],
    ],

    'tables' => [
        'tasks' => 'ffhs_tasks',
        'task_assignables' => 'ffhs_task_assignables',
        'task_watchables' => 'ffhs_task_watchables',
        'task_notification_log' => 'ffhs_task_notification_log',
    ],

    'models' => [
        Task::class => Task::class,
        NotificationLog::class => NotificationLog::class,
    ],

    'user' => [
        'model' => User::class,
        'name_attribute' => 'name',
    ],

    'assignable_models' => [
        User::class,
        FirstUserGroup::class,
        SecondUserGroup::class,
    ],

    'types' => [
        ApprovalTaskType::class,
        DefaultTaskType::class,
    ],
];
