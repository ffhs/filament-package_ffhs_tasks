<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes;

use Ffhs\FfhsTasks\TaskType\TaskType;

class NonExpirableTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'non-expirable';
    }

    public static function displayname(): string
    {
        return 'Non-Expirable Task';
    }

    public function hasDeadline(): bool
    {
        return true;
    }

    public function canExpireAfterDeadline(): bool
    {
        return false;
    }
}
