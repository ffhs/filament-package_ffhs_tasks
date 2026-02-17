<?php

// config for Ffhs/FfhsTasks
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\DefaultTaskType;

return [
    'run_migrations' => true,

    'tables' => [
        'tasks' => 'ffhs_tasks',
        'task_assignables' => 'ffhs_task_assignables',
    ],

    'models' => [
        Task::class => Task::class,
    ],

    'user' => [
        'model' => User::class,
        'name_attribute' => 'name',
    ],

    'assignable_models' => [

    ],

    'types' => [
        DefaultTaskType::class,
    ],
];
