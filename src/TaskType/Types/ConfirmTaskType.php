<?php

namespace Ffhs\FfhsTasks\TaskType\Types;

use Ffhs\FfhsTasks\TaskType\TaskType;

class ConfirmTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'confirm';
    }

    public function getSettingSchema(): array
    {
        return [];
    }


    public function canBeDoneRemote(): bool
    {
        return false;
    }
}
