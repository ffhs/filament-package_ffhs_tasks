<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes;

use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;

class TestTaskType extends TaskType
{
    public static bool $mutateDataBeforeCompleteCalled = false;
    public static bool $mutateDataBeforeCancelCalled = false;
    public static bool $mutateDataBeforeExpireCalled = false;
    public static bool $mutateDataBeforeSaveCalled = false;

    public static bool $afterCompleteCalled = false;
    public static bool $afterCancelCalled = false;
    public static bool $afterExpireCalled = false;
    public static bool $afterSaveCalled = false;

    public static function identifier(): string
    {
        return 'test-1';
    }

    public static function displayname(): string
    {
        return 'Test 1';
    }

    public function canBeCancelled(): bool
    {
        return true;
    }

    public static function resetFlags(): void
    {
        self::$mutateDataBeforeCompleteCalled = false;
        self::$mutateDataBeforeCancelCalled = false;
        self::$mutateDataBeforeExpireCalled = false;
        self::$mutateDataBeforeSaveCalled = false;
        self::$afterCompleteCalled = false;
        self::$afterCancelCalled = false;
        self::$afterExpireCalled = false;
        self::$afterSaveCalled = false;
    }

    /** Helpers for lifecycle testing */
    public function mutateDataBeforeComplete(Task $record, array $data): array
    {
        self::$mutateDataBeforeCompleteCalled = true;

        return $data;
    }

    public function mutateDataBeforeCancel(Task $record, array $data): array
    {
        self::$mutateDataBeforeCancelCalled = true;

        return $data;
    }

    public function mutateDataBeforeExpire(Task $record, array $data): array
    {
        self::$mutateDataBeforeExpireCalled = true;

        return $data;
    }

    public function mutateDataBeforeSave(Task $record, array $data): array
    {
        self::$mutateDataBeforeSaveCalled = true;

        return $data;
    }

    public function afterComplete(Task $record): void
    {
        self::$afterCompleteCalled = true;
    }

    public function afterCancel(Task $record): void
    {
        self::$afterCancelCalled = true;
    }

    public function afterExpire(Task $record): void
    {
        self::$afterExpireCalled = true;
    }

    public function afterSave(Task $record): void
    {
        self::$afterSaveCalled = true;
    }
}
