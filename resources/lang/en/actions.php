<?php

return [
    'back' => 'Back',

    'handle' => [
        'label' => 'Handle',
    ],

    'complete' => [
        'label' => 'Complete Task',

        'notification' => [
            'title' => 'Completed',
            'body' => 'Task was successfully completed.',
        ]
    ],

    'cancel' => [
        'label' => 'Cancel Task',

        'notification' => [
            'title' => 'Cancelled',
            'body' => 'Task was cancelled.',
        ]
    ],

    'group_assign' => [
        'label' => 'Assign',
    ],

    'assign_me' => [
        'label' => 'Assign me',
    ],

    'unassign_me' => [
        'label' => 'Unassign me',
    ],

    'assign_group' => [
        'label' => 'Assign group',
    ],

    'assign_user' => [
        'label' => 'Assign user',

        'schema' => [
            'users' => [
                'label' => 'Users',
                'helper_text' => 'Select users to assign to this task',
            ],
        ],
    ],
];
