<?php

namespace Ffhs\FfhsTasks\Jobs;

use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Actions\SendTaskNotification;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskDeadlineApproachingNotification;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Ffhs\FfhsTasks\Traits\ChecksNotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class SendDeadlineApproachingNotificationsJob implements ShouldQueue
{
    use ChecksNotificationLog;
    use Queueable;

    public function handle(SendTaskNotification $sender): void
    {
        if (! in_array(TaskDeadlineApproachingNotification::class, config('ffhs-tasks.notifications.enabled', []))) {
            return;
        }

        foreach (TaskType::getAllTypes() as $typeClass) {
            /** @var TaskType $taskType */
            $taskType = $typeClass::make();
            $intervals = $taskType->deadlineRemindBefore();

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
            $targetDate = Carbon::now()->add($interval);
            $notificationKey = 'before_'.self::intervalKey($interval);

            Task::query()
                ->where('type', $taskType::identifier())
                ->where('status', TaskStatus::InProgress)
                ->whereNotNull('deadline_at')
                ->whereDate('deadline_at', '<=', $targetDate)
                ->whereDate('deadline_at', '>', Carbon::now())
                ->whereDoesntHave(
                    'notificationLogs',
                    fn (Builder $query) => $query
                        ->where('notification_type', TaskDeadlineApproachingNotification::class)
                        ->where('notification_key', $notificationKey)
                )
                ->lazyById(100)
                ->each(function (Task $task) use ($sender, $interval, $notificationKey): void {
                    $sender->execute($task, new TaskDeadlineApproachingNotification($task, $interval));
                    $this->markAsSent($task, TaskDeadlineApproachingNotification::class, $notificationKey);
                });
        }
    }

}
