<?php

namespace Ffhs\FfhsTasks\TaskType;

use Closure;

class DefaultTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'default';
    }

    public static function displayname(): string
    {
        return 'Default';
    }

    public function hasStartDate(): bool
    {
        return false;
    }

    public function hasDeadline(): bool
    {
        return false;
    }

    public function canBeCancelled(): bool
    {
        return true;
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
