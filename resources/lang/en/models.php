<?php

// translations for Ffhs/FfhsTasks

return [
    'tasks' => [
        'label' => [
            'singular' => 'Task',
            'plural' => 'Tasks',
        ],

        'resource' => [
            'group' => 'Tasks',
            'navigation_label' => 'Tasks',

            'pages' => [
                'all' => [
                    'navigation_label' => 'All Tasks',
                    'title' => 'All Tasks',
                ],
                'archive' => [
                    'navigation_label' => 'Archived Tasks',
                    'title' => 'Archived Tasks',
                ],
                'index' => [
                    'navigation_label' => 'Tasks',
                    'title' => 'Tasks',
                    'tabs' => [
                        'my' => 'My Tasks',
                        'created' => 'Created Tasks',
                        'groups' => 'Group Tasks',
                    ],
                ],
            ],
        ],
        'relations' => [
            'users' => [
                'label' => 'Assigned Users',
            ],
            'taskUserGroups' => [
                'label' => 'Assigned Groups',
            ],
        ],
        'attributes' => [
            'state' => [
                'label' => 'Status',
            ],
            'can_cancel' => [
                'label' => 'Can Be Cancelled',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
            ],
            'title' => [
                'label' => 'Task Title',
                'helper_text' => '',
            ],
            'finished' => [
                'label' => 'Finished',
                'helper_text' => '',
            ],
            'creator' => [
                'label' => 'Creator',
                'helper_text' => '',
            ],
            'description' => [
                'label' => 'Description',
                'helper_text' => '',
            ],
            'type' => [
                'label' => 'Task Type',
                'helper_text' => '',
            ],
            'start_at' => [
                'label' => 'Start',
                'helper_text' => '',
            ],
            'deadline_at' => [
                'label' => 'Deadline',
                'helper_text' => '',
            ],
        ],
        'actions' => [
            'to_admin_side' => [
                'label' => 'All Tasks',
                'tooltip' => '',
            ],
            'assign_me' => [
                'label' => 'Assign to Me',
                'tooltip' => '',
            ],
            'assign_group' => [
                'label' => 'Assign Group',
                'tooltip' => '',
            ],
            'assign_user' => [
                'label' => 'Assign Person',
                'tooltip' => '',
                'schema' => [
                    'users' => [
                        'label' => 'People',
                        'helper_text' => '',
                    ],
                ],
            ],
            'assign_user_admin' => [
                'label' => 'Assign Person',
                'tooltip' => '',
            ],
            'unassign_me' => [
                'label' => 'Remove Me',
                'tooltip' => '',
            ],
            'cancel' => [
                'label' => 'Cancel',
            ],
            'finish' => [
                'label' => 'Complete',
            ],
        ],
    ],

    'task_user_group' => [

    ],
];
