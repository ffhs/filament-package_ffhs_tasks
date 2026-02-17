<?php

namespace Ffhs\FfhsTasks\Scopes;

use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Ffhs\FfhsTasks\Enums\TaskPrivacy;
use Ffhs\FfhsTasks\Support\AssignableHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

final class PrivacyScope
{
    public function __invoke(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query
                ->where('privacy', TaskPrivacy::Public)
                ->orWhere(function (Builder $query) {
                    $query
                        ->where('privacy', TaskPrivacy::Private)
                        ->where(function (Builder $query) {
                            $this->filterByCreator($query);
                            $this->filterByUserAssignables($query, 'assignables');
                            $this->filterByUserAssignables($query, 'watchables');
                        });
                });
        });
    }

    private function filterByCreator(Builder $query): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $query->orWhere(function (Builder $query) use ($user) {
            $query
                ->where('creator_type', $user->getMorphClass())
                ->where('creator_id', $user->getKey());
        });
    }

    private function filterByUserAssignables(Builder $query, string $relation): void
    {
        $groups = AssignableHelper::assignablesForUser();

        if ($groups->isEmpty()) {
            return;
        }

        $groupedGroups = $groups->groupBy(fn (AssignableInterface $group) => $group::class);

        $query->orWhereHas($relation, function (Builder $query) use ($groupedGroups) {
            $query->where(function (Builder $query) use ($groupedGroups) {
                foreach ($groupedGroups as $groupClass => $groups) {
                    $morphAlias = Relation::getMorphAlias($groupClass);
                    $keys = $groups->map(fn (AssignableInterface&Model $group) => $group->getKey());

                    $query->orWhere(
                        fn (Builder $query) => $query
                            ->where('assignable_type', $morphAlias)
                            ->whereIn('assignable_id', $keys)
                    );
                }
            });
        });
    }
}
