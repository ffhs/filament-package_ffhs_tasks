<?php

namespace Ffhs\FfhsTasks\TaskType;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Traits\HasTaskLifeCycle;
use Ffhs\FfhsUtils\Contracts\Type;
use Ffhs\FfhsUtils\Traits\IsType;
use Illuminate\Contracts\Translation\Translator;

abstract class TaskType implements Type
{
    use HasTaskLifeCycle;
    use IsType;

    protected bool $canBeDoneRemotely = false;

    protected bool $canBeSavedWithoutFinish = true;

    public static function getTypeListConfig(): array
    {
        return FfhsTasks::config('types');
    }

    public static function __(string $key): Translator|string|array|null
    {
        return FfhsTasks::__('task_types.'.static::identifier().'.'.$key);
    }

    public static function displayname(): string
    {
        return static::__('label');
    }

    public function getSettingSchema(): array|\Closure
    {
        return [];
    }

    public function getHandleSchema(): array|\Closure
    {
        return [];
    }

    public function canBeDoneRemote(): bool
    {
        return $this->canBeDoneRemotely;
    }

    public function canBeSavedWithoutFinish(): bool
    {
        return $this->canBeSavedWithoutFinish;
    }
}
