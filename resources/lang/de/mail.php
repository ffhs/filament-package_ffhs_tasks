<?php

return [
    'task_assigned' => [
        'subject' => 'Neue Aufgabe zugewiesen: :title',
        'greeting' => 'Hallo!',
        'line1' => 'Dir wurde die Aufgabe „:title" zugewiesen.',
        'line2' => 'Bitte prüfe die Details und handle entsprechend.',
        'action' => 'Aufgabe ansehen',
    ],

    'deadline_approaching' => [
        'subject' => 'Frist nähert sich: :title (:time verbleibend)',
        'greeting' => 'Hallo!',
        'line1' => 'Die Aufgabe „:title" hat eine Frist in :time.',
        'line2' => 'Bitte stelle sicher, dass sie rechtzeitig erledigt wird.',
        'action' => 'Aufgabe ansehen',
    ],

    'deadline_approaching_immediate' => [
        'subject' => 'Frist steht unmittelbar bevor: :title',
        'greeting' => 'Hallo!',
        'line1' => 'Die Aufgabe „:title" erreicht gleich ihre Frist.',
        'line2' => 'Bitte stelle sicher, dass sie rechtzeitig erledigt wird.',
        'action' => 'Aufgabe ansehen',
    ],

    'deadline_exceeded' => [
        'subject' => 'Frist überschritten: :title (:time überfällig)',
        'greeting' => 'Hallo!',
        'line1' => 'Die Frist der Aufgabe „:title" wurde um :time überschritten und sie ist noch in Bearbeitung.',
        'line2' => 'Bitte handle so schnell wie möglich.',
        'action' => 'Aufgabe ansehen',
    ],

    'deadline_exceeded_immediate' => [
        'subject' => 'Frist überschritten: :title',
        'greeting' => 'Hallo!',
        'line1' => 'Die Frist der Aufgabe „:title" ist soeben abgelaufen und sie ist noch in Bearbeitung.',
        'line2' => 'Bitte handle so schnell wie möglich.',
        'action' => 'Aufgabe ansehen',
    ],

    'status_changed' => [
        'subject' => 'Aufgabenstatus geändert: :title',
        'greeting' => 'Hallo!',
        'line1' => 'Die Aufgabe „:title" wurde als :status markiert.',
        'action' => 'Aufgabe ansehen',
    ],

    'start_date_reached' => [
        'subject' => 'Aufgabe ist jetzt aktiv: :title',
        'greeting' => 'Hallo!',
        'line1' => 'Die Aufgabe „:title" hat ihr Startdatum erreicht und ist jetzt aktiv.',
        'line2' => 'Du kannst nun mit der Bearbeitung beginnen.',
        'action' => 'Aufgabe ansehen',
    ],

    'weekly_tasks' => [
        'subject' => 'Deine Aufgaben diese Woche (:from – :to)',
        'greeting' => 'Hallo!',
        'line1' => 'Hier sind deine Aufgaben mit Fristen diese Woche (:from – :to):',
        'line2' => 'Bitte stelle sicher, dass alle Aufgaben rechtzeitig erledigt werden.',
    ],
];
