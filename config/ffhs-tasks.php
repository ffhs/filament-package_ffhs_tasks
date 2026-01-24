<?php

// config for Ffhs/FfhsTasks
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\Types\ConfirmTaskType;

return [
    'table_names' => [
        'tasks' => 'ffhs_tasks',
        'task_user' => 'ffhs_task_user',
        'task_user_group' => 'ffhs_task_user_group',
    ],

    'models' => [
        'tasks' => Task::class,
    ],

    'user' => [
        'name_attribute' => 'name',
    ],

    'user_groups' => [

    ],

    'types' => [
        ConfirmTaskType::class,
    ],

    'user_creatable_types' => [
        ConfirmTaskType::class,
    ],
];
