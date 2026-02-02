<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes;

use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;

class ExpirableTaskType extends TaskType
{
    public static bool $mutateDataBeforeExpireCalled = false;

    public static bool $afterExpireCalled = false;

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

    public static function resetFlags(): void
    {
        self::$mutateDataBeforeExpireCalled = false;
        self::$afterExpireCalled = false;
    }

    public function mutateDataBeforeExpire(Task $record, array $data): array
    {
        self::$mutateDataBeforeExpireCalled = true;

        return $data;
    }

    public function afterExpire(Task $record): void
    {
        self::$afterExpireCalled = true;
    }
}
