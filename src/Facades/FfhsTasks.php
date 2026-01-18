<?php

namespace Ffhs\FfhsTasks\Facades;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Ffhs\FfhsTasks\FfhsTasks
 *
 * @method static mixed config(string ...$keys)
 * @method static array|string|Translator|null __(string $string)
 * @method static Builder|Relation modifyQueryActiveTask($baseQuery)
 * @method static Builder|Relation modifyQueryArchiveTasks($baseQuery)
 * @method static Collection taskServers()
 * @method static array userGroups()
 */
class FfhsTasks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ffhs\FfhsTasks\FfhsTasks::class;
    }
}
