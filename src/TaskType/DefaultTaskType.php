<?php

namespace Ffhs\FfhsTasks\TaskType;

use Closure;

class DefaultTaskType extends TaskType
{
    protected static bool $hasStartDate = false;
    protected static bool $hasDeadline = false;
    protected static bool $canBeCancelled = true;
    protected static string $identifier = 'default';

    public static function displayname(): string
    {
        return 'Default';
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
