<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes;

use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;

class TestTaskType extends TaskType
{
    public static bool $afterCompleteCalled = false;
    public static bool $afterCancelCalled = false;

    public static function identifier(): string
    {
        return 'test-1';
    }

    public static function displayname(): string
    {
        return 'Test 1';
    }

    public function afterComplete(Task $record, array $getState): void
    {
        self::$afterCompleteCalled = true;
    }

    public function afterCancel(Task $record, array $getState): void
    {
        self::$afterCancelCalled = true;
    }
}
