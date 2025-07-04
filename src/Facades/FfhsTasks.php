<?php

namespace Ffhs\FfhsTasks\Facades;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Ffhs\FfhsTasks\FfhsTasks
 * @method static mixed config(string ...$keys)
 * @method static array|string|Translator|null __(string $string)
 */
class FfhsTasks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ffhs\FfhsTasks\FfhsTasks::class;
    }
}
