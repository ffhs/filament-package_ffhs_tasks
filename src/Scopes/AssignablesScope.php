<?php

namespace Ffhs\FfhsTasks\Scopes;

use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Ffhs\FfhsTasks\Support\AssignableHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

final class AssignablesScope
{
    public function __invoke(Builder $query): Builder
    {
        $query->whereHas('assignables', function (Builder $query) {
            $groups = AssignableHelper::assignablesForUser();

            $groupedGroups = $groups->groupBy(fn (AssignableInterface $group) => $group::class);

            $query->where(function (Builder $query) use ($groupedGroups) {
                foreach ($groupedGroups as $groupClass => $groups) {
                    $groupClass = Relation::getMorphAlias($groupClass);
                    $keys = $groups->map(fn (AssignableInterface&Model $group) => $group->getKey());

                    $query->orWhere(
                        fn (Builder $query) => $query
                            ->where('assignable_type', $groupClass)
                            ->whereIn('assignable_id', $keys)
                    );
                }
            });
        });

        return $query;
    }
}
