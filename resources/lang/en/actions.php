<?php

return [
    'back' => 'Back',

    'handle' => [
        'label' => 'Handle',
    ],

    'complete' => [
        'label' => 'Complete',

        'notification' => [
            'title' => 'Completed',
            'body' => 'Task was successfully completed.',
        ]
    ],

    'cancel' => [
        'label' => 'Cancel',

        'notification' => [
            'title' => 'Cancelled',
            'body' => 'Task was cancelled.',
        ]
    ],

    'assign_me' => [
        'label' => 'Assign me',
        'tooltip' => 'Assign this task to yourself',
    ],

    'unassign_me' => [
        'label' => 'Unassign me',
        'tooltip' => 'Remove yourself from this task',
    ],

    'assign_group' => [
        'label' => 'Assign group',
        'tooltip' => 'Assign this task to a group',
    ],

    'assign_user' => [
        'label' => 'Assign user',
        'tooltip' => 'Assign this task to a user',

        'schema' => [
            'users' => [
                'label' => 'Users',
                'helper_text' => 'Select users to assign to this task',
            ],
        ],
    ],
];
