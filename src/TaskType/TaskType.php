<?php

namespace Ffhs\FfhsTasks\TaskType;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsUtils\Contracts\Type;
use Ffhs\FfhsUtils\Traits\IsType;
use Illuminate\Contracts\Translation\Translator;

abstract class TaskType implements Type
{
    use IsType;

    public static function getTypeListConfig(): array
    {
        return FfhsTasks::config('types');
    }

    public static function __(string $key): Translator|string|array|null
    {
        return FfhsTasks::__('task_types.' . static::identifier() . '.' . $key);
    }

    public static function displayname(): string
    {
        return static::__('label');
    }

    abstract public function getSettingSchema(): array;
}
