<?php

namespace Ffhs\FfhsTasks\Notifications;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStartDateReachedNotification extends Notification
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

        $params = ['title' => $this->task->title];

        return (new MailMessage())
            ->subject(__('ffhs-tasks::mail.start_date_reached.subject', $params))
            ->greeting(__('ffhs-tasks::mail.start_date_reached.greeting'))
            ->line(__('ffhs-tasks::mail.start_date_reached.line1', $params))
            ->line(__('ffhs-tasks::mail.start_date_reached.line2', $params))
            ->action(__('ffhs-tasks::mail.start_date_reached.action'), TaskResource::getUrl('edit', ['record' => $this->task]));
    }
}
