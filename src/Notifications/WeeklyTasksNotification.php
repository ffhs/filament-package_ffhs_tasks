<?php

namespace Ffhs\FfhsTasks\Notifications;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class WeeklyTasksNotification extends Notification
{
    /**
     * @param  Collection<int, Task>  $tasks
     */
    public function __construct(
        public Collection $tasks,
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
        $firstTask = $this->tasks->first();
        $taskType = $firstTask?->getType();

        if ($taskType) {
            $customMail = $taskType->getMailForNotification($this, $firstTask);

            if ($customMail) {
                return $customMail;
            }
        }

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $params = [
            'from' => $weekStart->translatedFormat('d.m.Y'),
            'to' => $weekEnd->translatedFormat('d.m.Y'),
        ];

        $mail = (new MailMessage())
            ->subject(__('ffhs-tasks::mail.weekly_tasks.subject', $params))
            ->greeting(__('ffhs-tasks::mail.weekly_tasks.greeting'))
            ->line(__('ffhs-tasks::mail.weekly_tasks.line1', $params));

        foreach ($this->tasks as $task) {
            $deadline = $task->deadline_at->translatedFormat('d.m.Y');
            $url = TaskResource::getUrl('edit', ['record' => $task]);
            $mail->line("- [{$task->title} — {$deadline}]({$url})");
        }

        $mail->line(__('ffhs-tasks::mail.weekly_tasks.line2'));

        return $mail;
    }
}
