<?php

namespace Ffhs\FfhsTasks\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ffhs\FfhsTasks\FfhsTasks
 */
class FfhsTasks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ffhs\FfhsTasks\FfhsTasks::class;
    }
}
