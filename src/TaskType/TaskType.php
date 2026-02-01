<?php

namespace Ffhs\FfhsTasks\TaskType;

use Closure;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Traits\HasTaskLifeCycle;
use Ffhs\FfhsUtils\Contracts\Type;
use Ffhs\FfhsUtils\Traits\IsType;

abstract class TaskType implements Type
{
    use HasTaskLifeCycle;
    use IsType;

    public static function getTypeListConfig(): array
    {
        return config('ffhs-tasks.types');
    }

    public function canBeCreatedViaUi(): bool
    {
        return true;
    }

    public function canBeCancelled(): bool
    {
        return false;
    }

    public function hasStartDate(): bool
    {
        return false;
    }

    public function hasDeadline(): bool
    {
        return false;
    }

    public function shouldExpireAfterDeadline(): bool
    {
        return false;
    }

    public function canViewTask(Task $task): bool
    {
        return auth()->user()->can('view', $task);
    }

    public function canEditTask(Task $task): bool
    {
        return auth()->user()->can('update', $task);
    }

    public function canHandleTask(Task $task): bool
    {
        return auth()->user()->can('handle', $task) && ($task->starts_at === null || $task->starts_at->isPast());
    }

    public function getMainComponents(): array|Closure
    {
        return [];
    }

    public function getSidebarComponents(): array|Closure
    {
        return [];
    }

    public function getHandleComponents(): array|Closure
    {
        return [];
    }
}
