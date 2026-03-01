<?php

use App\Models\CustomNotificationLog;
use App\Models\CustomTask;
use App\Models\FirstUserGroup;
use App\Models\SecondUserGroup;
use App\Models\User;
use App\TaskTypes\ApprovalTaskType;
use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Models\NotificationLog;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\Watchable;
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
        'tasks' => 'tasks',
        'task_assignables' => 'task_assignables',
        'task_watchables' => 'task_watchables',
        'task_notification_log' => 'task_notification_log',
        'task_tag' => 'task_tag',
        'task_tags' => 'task_tags',
    ],

    'models' => [
        Task::class => CustomTask::class,
        NotificationLog::class => CustomNotificationLog::class,
        Assignable::class => Assignable::class,
        Watchable::class => Watchable::class,
    ],

    'user' => [
        'model' => User::class,
        'name_attribute' => 'displayName',
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
