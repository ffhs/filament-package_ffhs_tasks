<?php

namespace Ffhs\FfhsTasks\Traits;

use Carbon\CarbonInterval;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

trait HasMailTexts
{
    /**
     * Customize the mail message for a given notification.
     * Return a MailMessage to override the default, or null to use the translation-based mail.
     */
    public function getMailForNotification(Notification $notification, Task $task): ?MailMessage
    {
        return null;
    }

    /**
     * @return array<CarbonInterval>
     */
    public function deadlineRemindBefore(): array
    {
        return config('ffhs-tasks.notifications.deadline_remind_before', []);
    }

    /**
     * @return array<CarbonInterval>
     */
    public function deadlineRemindAfter(): array
    {
        return config('ffhs-tasks.notifications.deadline_remind_after', []);
    }
}
