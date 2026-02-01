<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Models\Task;

trait HasTaskLifeCycle
{
    public function mutateDataBeforeCancel(Task $record, $data): array
    {
        return $this->mutateDataBeforeSave($record, $data);
    }

    public function mutateDataBeforeFinish(Task $record, array $data): array
    {
        return $this->mutateDataBeforeSave($record, $data);
    }

    public function mutateDataBeforeSave(Task $record, array $data): array
    {
        return $data;
    }

    public function afterCancel(Task $record, array $getState): void
    {
        $this->afterSave($record, $getState);
    }

    public function afterComplete(Task $record, array $getState): void
    {
        $this->afterSave($record, $getState);
    }

    public function afterSave(Task $record, array $getState): void
    {
    }
}
