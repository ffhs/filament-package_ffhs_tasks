<?php

namespace Ffhs\FfhsTasks\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class CreatorScope
{
    public function __construct(
        public ?Model $creator = null
    ) {
        $this->creator ??= auth()->user();
    }

    public function __invoke(Builder $query): Builder
    {
        $query
            ->where('creator_type', $this->creator::class)
            ->where('creator_id', $this->creator->getKey());

        return $query;
    }
}
