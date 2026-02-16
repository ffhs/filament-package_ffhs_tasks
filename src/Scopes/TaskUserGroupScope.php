<?php

namespace Ffhs\FfhsTasks\Scopes;

use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Ffhs\FfhsTasks\Support\UserGroupsHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

final class TaskUserGroupScope
{
    public function __invoke(Builder $query): Builder
    {
        $query->whereHas('taskUserGroups', function (Builder $query) {
            $groups = UserGroupsHelper::groupsForUser();

            $groupedGroups = $groups->groupBy(fn (TaskUserGroupInterface $group) => $group::class);

            $query->where(function (Builder $query) use ($groupedGroups) {
                foreach ($groupedGroups as $groupClass => $groups) {
                    $groupClass = Relation::getMorphAlias($groupClass);
                    $keys = $groups->map(fn (TaskUserGroupInterface&Model $group) => $group->getKey());

                    $query->orWhere(
                        fn (Builder $query) => $query
                            ->where('user_group_type', $groupClass)
                            ->whereIn('user_group_id', $keys)
                    );
                }
            });
        });

        return $query;
    }
}
