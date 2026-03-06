<?php

return [
    'task_assigned' => [
        'subject' => 'You have been assigned a new task: :title',
        'greeting' => 'Hello!',
        'line1' => 'You have been assigned to the task ":title".',
        'line2' => 'Please review the task details and take action accordingly.',
        'action' => 'View Task',
    ],

    'deadline_approaching' => [
        'subject' => 'Task deadline approaching: :title (:time remaining)',
        'greeting' => 'Hello!',
        'line1' => 'The task ":title" has a deadline approaching in :time.',
        'line2' => 'Please ensure it is completed on time.',
        'action' => 'View Task',
    ],

    'deadline_approaching_immediate' => [
        'subject' => 'Task deadline imminent: :title',
        'greeting' => 'Hello!',
        'line1' => 'The task ":title" is about to reach its deadline.',
        'line2' => 'Please ensure it is completed on time.',
        'action' => 'View Task',
    ],

    'deadline_exceeded' => [
        'subject' => 'Task deadline exceeded: :title (:time overdue)',
        'greeting' => 'Hello!',
        'line1' => 'The deadline for the task ":title" has been exceeded by :time and it is still in progress.',
        'line2' => 'Please take action as soon as possible.',
        'action' => 'View Task',
    ],

    'deadline_exceeded_immediate' => [
        'subject' => 'Task deadline exceeded: :title',
        'greeting' => 'Hello!',
        'line1' => 'The deadline for the task ":title" has just passed and it is still in progress.',
        'line2' => 'Please take action as soon as possible.',
        'action' => 'View Task',
    ],

    'status_changed' => [
        'subject' => 'Task status changed: :title',
        'greeting' => 'Hello!',
        'line1' => 'The task ":title" has been marked as :status.',
        'action' => 'View Task',
    ],

    'start_date_reached' => [
        'subject' => 'Task is now active: :title',
        'greeting' => 'Hello!',
        'line1' => 'The task ":title" has reached its start date and is now active.',
        'line2' => 'You can begin working on this task.',
        'action' => 'View Task',
    ],

    'weekly_tasks' => [
        'subject' => 'Your tasks this week (:from – :to)',
        'greeting' => 'Hello!',
        'line1' => 'Here are your tasks with deadlines this week (:from – :to):',
        'line2' => 'Please ensure all tasks are completed on time.',
    ],
];
