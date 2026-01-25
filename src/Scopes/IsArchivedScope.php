<?php

namespace Ffhs\FfhsTasks\Scopes;

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;

final class IsArchivedScope
{
    public function __invoke(Builder $query): Builder
    {
        return $query->whereNot('status', TaskStatus::InProgress);
        // return $query->where(function ($query) {
        //     $query
        //         ->whereNotNull('finished_at')
        //         ->orWhereNotNull('cancelled_at')
        //         ->orWhere('deadline_at', '<=', now());
        // });
    }
}
