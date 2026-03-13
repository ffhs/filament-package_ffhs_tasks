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
    'run_migrations' => true,

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

    'tables' => [
        'tasks' => 'ffhs_tasks',
        'task_assignables' => 'ffhs_task_assignables',
        'task_watchables' => 'ffhs_task_watchables',
        'task_notification_log' => 'ffhs_task_notification_log',
        'task_tag' => 'ffhs_task_tag',
        'task_tags' => 'ffhs_task_tags',
    ],

    'models' => [
        Task::class => Task::class,
        NotificationLog::class => NotificationLog::class,
        Assignable::class => Assignable::class,
        Watchable::class => Watchable::class,
    ],

    'user' => [
        'model' => User::class,
        'name_attribute' => 'name',
    ],

    'assignable_models' => [
        User::class,
    ],

    'types' => [
        DefaultTaskType::class,
    ],
];
