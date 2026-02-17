<?php

return [
    'model_label' => 'Task',
    'plural_model_label' => 'Tasks',

    'navigation_group' => 'Tasks',
    'navigation_label' => 'Tasks',

    'attributes' => [
        'id' => 'ID',
        'status' => 'Status',
        'privacy' => 'Privacy',
        'type' => 'Type',
        'title' => 'Title',
        'description' => 'Description',
        'assignables' => 'Assigned To',
        'watchables' => 'Collaborators',
        'creator' => 'Creator',
        'starts_at' => 'Starts',
        'deadline_at' => 'Deadline',
        'can_be_cancelled' => 'Can be cancelled',
    ],
];
