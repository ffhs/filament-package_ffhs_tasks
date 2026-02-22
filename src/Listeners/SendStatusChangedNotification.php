<?php

namespace Ffhs\FfhsTasks\Listeners;

use Ffhs\FfhsTasks\Actions\SendTaskNotification;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\StatusChangedEvent;
use Ffhs\FfhsTasks\Notifications\TaskStatusChangedNotification;

class SendStatusChangedNotification
{
    public function __construct(
        private SendTaskNotification $sender,
    ) {
    }

    public function handle(StatusChangedEvent $event): void
    {
        if (! in_array(TaskStatusChangedNotification::class, config('ffhs-tasks.notifications.enabled', []))) {
            return;
        }

        $task = $event->task;

        $notifyStatuses = [
            TaskStatus::Completed,
            TaskStatus::Cancelled,
            TaskStatus::Expired,
        ];

        if (! in_array($task->status, $notifyStatuses)) {
            return;
        }

        $this->sender->execute(
            $task,
            new TaskStatusChangedNotification($task),
            auth()->user(),
        );
    }
}
