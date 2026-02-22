<?php

namespace Ffhs\FfhsTasks\Jobs;

use Ffhs\FfhsTasks\Actions\SendTaskNotification;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskStartDateReachedNotification;
use Ffhs\FfhsTasks\Traits\ChecksNotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class SendStartDateReachedNotificationsJob implements ShouldQueue
{
    use ChecksNotificationLog;
    use Queueable;

    public function handle(SendTaskNotification $sender): void
    {
        if (! in_array(TaskStartDateReachedNotification::class, config('ffhs-tasks.notifications.enabled', []))) {
            return;
        }

        Task::query()
            ->where('status', TaskStatus::InProgress)
            ->whereNotNull('starts_at')
            ->where('starts_at', '<=', Carbon::now())
            ->whereDoesntHave(
                'notificationLogs',
                fn (Builder $query) => $query
                    ->where('notification_type', TaskStartDateReachedNotification::class)
                    ->where('notification_key', 'reached')
            )
            ->lazyById(100)
            ->each(function (Task $task) use ($sender): void {
                $sender->execute($task, new TaskStartDateReachedNotification($task));
                $this->markAsSent($task, TaskStartDateReachedNotification::class, 'reached');
            });
    }
}
