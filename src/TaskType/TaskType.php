<?php

namespace Ffhs\FfhsTasks\TaskType;

use Closure;
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
