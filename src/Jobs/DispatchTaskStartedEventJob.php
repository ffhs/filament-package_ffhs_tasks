<?php

namespace Ffhs\FfhsTasks\Jobs;

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\TaskStartedEvent;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Traits\ChecksNotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class DispatchTaskStartedEventJob implements ShouldQueue
{
    use ChecksNotificationLog;
    use Queueable;

    public function handle(): void
    {
        Task::query()
            ->where('status', TaskStatus::InProgress)
            ->whereNotNull('starts_at')
            ->where('starts_at', '<=', Carbon::now())
            ->whereDoesntHave(
                'notificationLogs',
                fn (Builder $query) => $query
                    ->where('notification_type', TaskStartedEvent::class)
                    ->where('notification_key', 'dispatched')
            )
            ->lazyById(100)
            ->each(function (Task $task): void {
                event(new TaskStartedEvent($task));

                $this->markAsSent($task, TaskStartedEvent::class, 'dispatched');
            });
    }
}
