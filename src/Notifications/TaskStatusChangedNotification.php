<?php

namespace Ffhs\FfhsTasks\Notifications;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusChangedNotification extends Notification
{
    public function __construct(
        public Task $task,
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

        $params = [
            'title' => $this->task->title,
            'status' => $this->task->status->getLabel(),
        ];

        return (new MailMessage())
            ->subject(__('ffhs-tasks::mail.status_changed.subject', $params))
            ->greeting(__('ffhs-tasks::mail.status_changed.greeting'))
            ->line(__('ffhs-tasks::mail.status_changed.line1', $params))
            ->action(__('ffhs-tasks::mail.status_changed.action'), TaskResource::getUrl('edit', ['record' => $this->task]));
    }
}
