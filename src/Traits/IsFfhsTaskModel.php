<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Illuminate\Contracts\Translation\Translator;

trait IsFfhsTaskModel
{
    public static function __(string ...$keys): array|string|Translator|null
    {
        $key = implode('.', $keys);
        return FfhsTasks::__('models.' . static::configKey() . '.' . $key);
    }

    abstract public static function configKey(): string;

    public static function getConfigTable(): string
    {
        return FfhsTasks::config('table_names.' . static::configKey()) ?: app(static::class)->getTable();
    }

    public function getTable(): string
    {
        return static::getConfigTable();
    }
}
