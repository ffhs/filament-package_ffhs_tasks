<?php

namespace Ffhs\FfhsTasks\Traits;

use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Models\NotificationLog;
use Ffhs\FfhsTasks\Models\Task;

use function Ffhs\FfhsTasks\resolve_model_class;

trait ChecksNotificationLog
{
    private static function intervalKey(CarbonInterval $interval): string
    {
        return (int) $interval->totalHours . 'h';
    }

    private function markAsSent(Task $task, string $notificationType, string $key): void
    {
        $modelClass = resolve_model_class(NotificationLog::class);

        $modelClass::query()->updateOrCreate([
            'task_id' => $task->id,
            'notification_type' => $notificationType,
            'notification_key' => $key,
        ]);
    }
}
