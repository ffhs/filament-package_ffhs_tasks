<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes;

use Ffhs\FfhsTasks\TaskType\TaskType;

class ExpirableTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'expirable';
    }

    public static function displayname(): string
    {
        return 'Expirable Task';
    }

    public function hasDeadline(): bool
    {
        return true;
    }

    public function shouldExpireAfterDeadline(): bool
    {
        return true;
    }
}
