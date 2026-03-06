<?php

namespace Ffhs\FfhsTasks\Jobs;

use Carbon\CarbonImmutable;
use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\WeeklyTasksNotification;
use Ffhs\FfhsTasks\Traits\ChecksNotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

use function Ffhs\FfhsTasks\resolve_model_class;

class SendWeeklyTasksNotificationJob implements ShouldQueue
{
    use ChecksNotificationLog;
    use Queueable;

    public function handle(): void
    {
        if (! in_array(WeeklyTasksNotification::class, config('ffhs-tasks.notifications.enabled', []))) {
            return;
        }

        $now = CarbonImmutable::now();
        $weekStart = $now->startOfWeek();
        $weekEnd = $now->endOfWeek();
        $notificationKey = 'week_'.$now->format('Y_M');

        $tasks = resolve_model_class(Task::class)::query()
            ->where('status', TaskStatus::InProgress)
            ->whereNotNull('deadline_at')
            ->whereBetween('deadline_at', [$weekStart, $weekEnd])
            ->whereDoesntHave(
                'notificationLogs',
                fn (Builder $query) => $query
                    ->where('notification_type', WeeklyTasksNotification::class)
                    ->where('notification_key', $notificationKey)
            )
            ->with('assignables.assignable')
            ->get();

        if ($tasks->isEmpty()) {
            return;
        }

        /** @var Collection<string, array{user: Model, tasks: Collection<int, Task>}> $tasksByUser */
        $tasksByUser = collect();

        foreach ($tasks as $task) {
            foreach ($task->assignables as $pivot) {
                $assignable = $pivot->assignable;

                if (! $assignable) {
                    continue;
                }

                $this->collectUsersFromAssignable($assignable, $task, $tasksByUser);
            }
        }

        foreach ($tasksByUser as $entry) {
            $entry['user']->notify(new WeeklyTasksNotification($entry['tasks'])); /** @phpstan-ignore method.notFound */
        }

        foreach ($tasks as $task) {
            $this->markAsSent($task, WeeklyTasksNotification::class, $notificationKey);
        }
    }

    /**
     * @param  Collection<string, array{user: Model, tasks: Collection<int, Task>}>  $tasksByUser
     */
    private function collectUsersFromAssignable(Model $assignable, Task $task, Collection $tasksByUser): void
    {
        if ($assignable instanceof User && $this->isNotifiable($assignable)) {
            $this->addTaskForUser($assignable, $task, $tasksByUser);

            return;
        }

        if ($assignable instanceof AssignableInterface) {
            $assignable->usersQuery()->get()->each(function (Model $user) use ($task, $tasksByUser): void {
                if ($this->isNotifiable($user)) {
                    $this->addTaskForUser($user, $task, $tasksByUser);
                }
            });
        }
    }

    /**
     * @param  Collection<string, array{user: Model, tasks: Collection<int, Task>}>  $tasksByUser
     */
    private function addTaskForUser(Model $user, Task $task, Collection $tasksByUser): void
    {
        $key = $user->getMorphClass().'_'.$user->getKey();

        if (! $tasksByUser->has($key)) {
            $tasksByUser->put($key, ['user' => $user, 'tasks' => collect()]);
        }

        $tasksByUser->get($key)['tasks']->push($task);
    }

    private function isNotifiable(Model $model): bool
    {
        return in_array(Notifiable::class, class_uses_recursive($model));
    }
}
