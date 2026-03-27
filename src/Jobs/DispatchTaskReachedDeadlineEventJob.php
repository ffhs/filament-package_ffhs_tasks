<?php

namespace Ffhs\FfhsTasks\Jobs;

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\TaskReachedDeadlineEvent;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Traits\ChecksNotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class DispatchTaskReachedDeadlineEventJob implements ShouldQueue
{
    use ChecksNotificationLog;
    use Queueable;

    public function handle(): void
    {
        Task::query()
            ->where('status', TaskStatus::InProgress)
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '<=', Carbon::now())
            ->whereDoesntHave(
                'notificationLogs',
                fn (Builder $query) => $query
                    ->where('notification_type', TaskReachedDeadlineEvent::class)
                    ->where('notification_key', 'reached')
            )
            ->lazyById(100)
            ->each(function (Task $task): void {
                event(new TaskReachedDeadlineEvent($task));

                $this->markAsSent($task, TaskReachedDeadlineEvent::class, 'reached');
            });
    }
}
