<?php

namespace Ffhs\FfhsTasks\Jobs;

use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Actions\SendTaskNotification;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskDeadlineExceededNotification;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Ffhs\FfhsTasks\Traits\ChecksNotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class SendDeadlineExceededNotificationsJob implements ShouldQueue
{
    use ChecksNotificationLog;
    use Queueable;

    public function handle(SendTaskNotification $sender): void
    {
        if (! in_array(TaskDeadlineExceededNotification::class, config('ffhs-tasks.notifications.enabled', []))) {
            return;
        }

        foreach (TaskType::getAllTypes() as $typeClass) {
            /** @var TaskType $taskType */
            $taskType = $typeClass::make();
            $intervals = $taskType->deadlineRemindAfter();

            if (empty($intervals)) {
                continue;
            }

            $this->processIntervalsForType($sender, $taskType, $intervals);
        }
    }

    /**
     * @param array<CarbonInterval> $intervals
     */
    private function processIntervalsForType(SendTaskNotification $sender, TaskType $taskType, array $intervals): void
    {
        foreach ($intervals as $interval) {
            $notificationKey = 'after_'.self::intervalKey($interval);

            Task::query()
                ->where('type', $taskType::identifier())
                ->where('status', TaskStatus::InProgress)
                ->whereNotNull('deadline_at')
                ->where('deadline_at', '<=', Carbon::now()->sub($interval))
                ->whereDoesntHave(
                    'notificationLogs',
                    fn (Builder $query) => $query
                        ->where('notification_type', TaskDeadlineExceededNotification::class)
                        ->where('notification_key', $notificationKey)
                )
                ->lazyById(100)
                ->each(function (Task $task) use ($sender, $interval, $notificationKey): void {
                    $sender->execute($task, new TaskDeadlineExceededNotification($task, $interval));
                    $this->markAsSent($task, TaskDeadlineExceededNotification::class, $notificationKey);
                });
        }
    }
}
