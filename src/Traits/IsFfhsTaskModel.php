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

    public function getTable()
    {
        return FfhsTasks::config('table_names.' . $this::configKey()) ?: parent::getTable();
    }
}
