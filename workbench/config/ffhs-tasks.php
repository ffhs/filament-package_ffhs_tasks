<?php

use App\Models\FirstUserGroup;
use App\Models\SecondUserGroup;
use App\TaskTypes\ApprovalTaskType;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\DefaultTaskType;

return [
    'run_migrations' => true,

    'tables' => [
        'tasks' => 'ffhs_tasks',
        'task_user' => 'ffhs_task_user',
        'task_user_group' => 'ffhs_task_user_group',
    ],

    'models' => [
        Task::class => Task::class,
    ],

    'user' => [
        'name_attribute' => 'name',
    ],

    'user_groups' => [
        FirstUserGroup::class,
        SecondUserGroup::class,
    ],

    'types' => [
        ApprovalTaskType::class,
        DefaultTaskType::class,
    ],
];
