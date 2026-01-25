<?php

namespace Ffhs\FfhsTasks\Scopes;

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;

final class IsActiveScope
{
    public function __invoke(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::InProgress);
        // return $query->where(function (Builder $query) {
        //     $query
        //         ->whereNull('finished_at')
        //         ->whereNull('cancelled_at')
        //         ->where(function (Builder $query) {
        //             $query
        //                 ->whereNull('deadline_at')
        //                 ->orWhere('deadline_at', '>=', now());
        //         });
        // });
    }
}
