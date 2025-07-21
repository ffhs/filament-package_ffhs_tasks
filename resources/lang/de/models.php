<?php

// translations for Ffhs/FfhsTasks

return [
    'tasks' => [
        'label' => [
            'singular' => 'Aufgabe',
            'plural' => 'Aufgaben',
        ],
        'resource' => [
            'navigation-label' => 'Aufgaben',
            'pages' => [
                'admin-index' => [
                    'title' => 'Alle Aufgaben (Admin)',
                ],
                'index' => [
                    'title' => 'Meine Aufgaben',
                    'tabs' => [
                        'my' => 'Meine Aufgaben',
                        'archive' => 'Archiv',
                        'all' => 'Alle',
                        'created' => 'Erstellte Aufgaben'
                    ]
                ]
            ]
        ],
        'relations' => [
            'users' => [
                'label' => 'Zugewiesene Nutzer'
            ]
        ],
        'attributes' => [
            'state' => [
                'label' => 'Status',
            ],
            'can_cancel' => [
                'label' => 'Kann Abgebrochen werden'
            ],
            'cancelled' => [
                'label' => 'Abgebrochen'
            ],
            'title' => [
                'label' => 'Aufgaben Titel',
                'helper_text' => ''
            ],
            'finished' => [
                'label' => 'Erledigt',
                'helper_text' => ''
            ],
            'creator' => [
                'label' => 'Ersteller',
                'helper_text' => ''
            ],
            'description' => [
                'label' => 'Beschreibung',
                'helper_text' => ''
            ],
            'type' => [
                'label' => 'Aufgaben Type',
                'helper_text' => ''
            ],
            'start_at' => [
                'label' => 'Start',
                'helper_text' => ''
            ],
            'deadline_at' => [
                'label' => 'Deadline',
                'helper_text' => ''
            ],
        ],
        'actions' => [
            'to_admin_side' => [
                'label' => 'Alle Aufgaben',
                'tooltip' => '',
            ],
            'assign_me' => [
                'label' => 'Mir zuweisen',
                'tooltip' => '',
            ],
            'assign_group' => [
                'label' => 'Gruppe zuweisen',
                'tooltip' => '',
            ],
            'assign_user' => [
                'label' => 'Person zuweisen',
                'tooltip' => '',
                'schema' => [
                    'users' => [
                        'label' => 'Personen',
                        'helper_text' => '',
                    ]
                ]
            ],
            'assign_user_admin' => [
                'label' => 'Person zuweisen',
                'tooltip' => '',
            ],
            'unassign_me' => [
                'label' => 'Mich entfernen',
                'tooltip' => '',
            ],
            'cancel' => [
                'label' => 'Stonieren',
            ],
            'finish' => [
                'label' => 'Abschliessen',
            ]
        ],
    ],

    'task_servers' => [
        'label' => [
            'singular' => 'Aufgaben Anwendung',
            'plural' => 'Aufgaben Anwendungen',
        ],
        'resource' => [
            'navigation-label' => 'Anwendungen',
        ],
        'attributes' => [
            'title' => [
                'label' => 'App Name',
                'helper_text' => ''
            ],
            'token' => [
                'label' => 'Token',
                'helper_text' => ''
            ],
            'url' => [
                'label' => 'URL',
                'helper_text' => ''
            ],
            'created_at' => [
                'label' => 'Hinzugefügt am',
            ],
        ],
        'actions' => [
            'test_connection' => [
                'label' => 'Verbindung Testen',
                'short_label' => 'Test',
                'tooltip' => '',
            ]
        ]
    ],

    'task_user_group' => [

    ]
];
