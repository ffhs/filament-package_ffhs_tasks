<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Models\Task;

trait HasTaskLifeCycle
{
    public function mutateDataBeforeCancel(Task $record, array $data): array
    {
        return $data;
    }

    public function mutateDataBeforeComplete(Task $record, array $data): array
    {
        return $data;
    }

    public function mutateDataBeforeExpire(Task $record, array $data): array
    {
        return $data;
    }

    public function mutateDataBeforeSave(Task $record, array $data): array
    {
        return $data;
    }

    public function afterCancel(Task $record): void
    {
        //
    }

    public function afterComplete(Task $record): void
    {
        //
    }

    public function afterExpire(Task $record): void
    {
        //
    }

    public function afterSave(Task $record): void
    {
        //
    }
}
