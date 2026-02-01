<?php

// config for Ffhs/FfhsTasks
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\Types\ApprovalTaskType;

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

    ],

    'types' => [
        ApprovalTaskType::class,
    ],

    'user_creatable_types' => [
        ApprovalTaskType::class,
    ],
];
