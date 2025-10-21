<?php

namespace Ffhs\FfhsTasks;

use Ffhs\FfhsTasks\Models\TaskServer;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/**
 */
class FfhsTasks
{
    public function config(string ...$key)
    {
        if (empty($key)) {
            return config('ffhs-tasks');
        }

        return config('ffhs-tasks.' . implode('.', $key));
    }

    public function __(string ...$keys): array|string|Translator|null
    {
        $key = implode('.', $keys);
        return __('filament-package_ffhs_tasks::' . $key);
    }


    public function modifyQueryActiveTask(Builder|Relation &$baseQuery): Builder|Relation
    {
        return $baseQuery
            ->where(function (Builder|Relation $query) {
                return $query
                    ->where(function (Builder|Relation $q) {
                        $q->where('start_at', '<=', now())
                            ->orWhereNull('start_at');
                    })
                    ->whereNot('finished', true)
                    ->whereNot('cancelled', true);
            });
    }

    public function modifyQueryArchiveTasks(Builder|Relation $baseQuery): Builder|Relation
    {
        if (method_exists($baseQuery, 'withTrashed')) {
            $baseQuery = $baseQuery->withTrashed();
        }

        return $baseQuery
            ->where(function (Builder|Relation $query) {
                return $query
                    ->whereNotNull('deleted_at')
                    ->orWhere('finished', true)
                    ->orWhere('cancelled', true);
            });
    }

    public function taskServers(): Collection
    {
        return once(fn() => TaskServer::all());
    }

    public function userGroups(): array
    {
        return $this->config('user_groups');
    }
}
