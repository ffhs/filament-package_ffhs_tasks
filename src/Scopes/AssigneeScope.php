<?php

namespace Ffhs\FfhsTasks\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class AssigneeScope
{
    public function __construct(
        public ?Model $assignee = null
    ) {
        $this->assignee ??= auth()->user();
    }

    public function __invoke(Builder $query): Builder
    {
        return $query
            ->whereHas(
                'users',
                fn (Builder $query) => $query->whereKey($this->assignee->getKey())
            );
    }
}
