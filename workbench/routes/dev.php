<?php

use App\Models\User;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskAssignedNotification;
use Ffhs\FfhsTasks\Notifications\TaskDeadlineApproachingNotification;
use Ffhs\FfhsTasks\Notifications\TaskDeadlineExceededNotification;
use Ffhs\FfhsTasks\Notifications\TaskStartDateReachedNotification;
use Ffhs\FfhsTasks\Notifications\TaskStatusChangedNotification;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')->group(function () {
    $task = fn () => Task::query()->first() ?? new Task([
        'title' => 'Example Task',
        'status' => TaskStatus::InProgress,
        'starts_at' => now(),
        'deadline_at' => now()->addDays(3),
    ]);

    $user = fn () => User::query()->first();

    Route::get('/assigned', fn () => (new TaskAssignedNotification($task()))->toMail($user()));
    Route::get('/deadline-approaching', fn () => (new TaskDeadlineApproachingNotification($task(), 3))->toMail($user()));
    Route::get('/deadline-exceeded', fn () => (new TaskDeadlineExceededNotification($task()))->toMail($user()));
    Route::get('/start-date-reached', fn () => (new TaskStartDateReachedNotification($task()))->toMail($user()));
    Route::get('/status-changed', fn () => (new TaskStatusChangedNotification($task()))->toMail($user()));
});
