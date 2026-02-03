<?php

return [
    'model_label' => 'Task',
    'plural_model_label' => 'Tasks',

    'navigation_group' => 'Tasks',
    'navigation_label' => 'Tasks',

    'attributes' => [
        'id' => 'ID',
        'status' => 'Status',
        'type' => 'Type',
        'title' => 'Title',
        'description' => 'Description',
        'users' => 'Assignees',
        'groups' => 'Groups',
        'creator' => 'Creator',
        'starts_at' => 'Starts',
        'deadline_at' => 'Deadline',
        'can_be_cancelled' => 'Can be cancelled',
    ],
];
