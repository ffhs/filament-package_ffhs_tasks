<?php

namespace Ffhs\FfhsTasks\Notifications;

use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDeadlineExceededNotification extends Notification
{
    public function __construct(
        public Task $task,
        public CarbonInterval $overdueTime,
    ) {
    }

    /**
     * @return array<string>
     */
    public function via(object $notifiable): array
    {
        if (! in_array(static::class, config('ffhs-tasks.notifications.enabled', []))) {
            return [];
        }

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $taskType = $this->task->getType();

        if ($taskType) {
            $customMail = $taskType->getMailForNotification($this, $this->task);

            if ($customMail) {
                return $customMail;
            }
        }

        $params = ['title' => $this->task->title, 'time' => $this->overdueTime->forHumans()];
        $variant = $this->overdueTime->totalHours > 0 ? 'deadline_exceeded' : 'deadline_exceeded_immediate';

        return (new MailMessage())
            ->subject(__("ffhs-tasks::mail.{$variant}.subject", $params))
            ->greeting(__("ffhs-tasks::mail.{$variant}.greeting"))
            ->line(__("ffhs-tasks::mail.{$variant}.line1", $params))
            ->line(__("ffhs-tasks::mail.{$variant}.line2", $params))
            ->action(__("ffhs-tasks::mail.{$variant}.action"), TaskResource::getUrl('edit', ['record' => $this->task]));
    }
}
