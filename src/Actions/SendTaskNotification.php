<?php

namespace Ffhs\FfhsTasks\Actions;

use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class SendTaskNotification
{
    public function execute(Task $task, Notification $notification, ?Model $excludeActor = null): void
    {
        $assignables = $task->assignables()->with('assignable')->get();
        $watchables = $task->watchables()->with('assignable')->get();

        /** @phpstan-ignore argument.type */
        $pivots = $assignables->merge($watchables)->unique(
            fn (Model $pivot) => $pivot->assignable_type.'_'.$pivot->assignable_id,
        );

        foreach ($pivots as $pivot) {
            $assignable = $pivot->assignable;

            if (! $assignable) {
                continue;
            }

            $this->notifyModel($assignable, $notification, $excludeActor);
        }
    }

    public function notifyModel(Model $model, Notification $notification, ?Model $excludeActor = null): void
    {
        if ($excludeActor && $model->is($excludeActor)) {
            return;
        }

        if ($model instanceof User && $this->isNotifiable($model)) {
            $model->notify($notification); /** @phpstan-ignore method.notFound */

            return;
        }

        if ($model instanceof AssignableInterface) {
            $this->notifyGroup($model, $notification, $excludeActor);
        }
    }

    private function notifyGroup(Model&AssignableInterface $group, Notification $notification, ?Model $excludeActor = null): void
    {
        if ($this->isNotifiable($group) && method_exists($group, 'routeNotificationFor')) {
            $mailRoute = $group->routeNotificationFor('mail', $notification);

            if (! empty($mailRoute)) {
                $group->notify($notification); /** @phpstan-ignore method.notFound */

                return;
            }
        }

        $group->usersQuery()->get()->each(function (Model $user) use ($notification, $excludeActor): void {
            if (! $this->isNotifiable($user)) {
                return;
            }

            if ($excludeActor && $user->is($excludeActor)) {
                return;
            }

            $user->notify($notification); /** @phpstan-ignore method.notFound */
        });
    }

    private function isNotifiable(Model $model): bool
    {
        return in_array(Notifiable::class, class_uses_recursive($model));
    }
}
