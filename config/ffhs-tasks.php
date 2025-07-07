<?php

// config for Ffhs/FfhsTasks
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskServer;

return [
    'table_names' => [
        'tasks' => 'ffhs_tasks',
        'task_user' => 'ffhs_task_user',
        'task_user_group' => 'ffhs_task_user_group',
        'task_servers' => 'ffhs_task_servers',
    ],


    'models' => [
        'task_servers' => TaskServer::class,
        'tasks' => Task::class,
    ]
];
