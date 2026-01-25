<?php

namespace Ffhs\FfhsTasks\Scopes;

use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Ffhs\FfhsTasks\Models\TaskUserGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class TaskGroupScope
{
    public function __invoke(Builder $query): Builder
    {
        return $query->whereHas('taskUserGroups', function (Builder $query) {
            $groups = TaskUserGroup::all();

            foreach ($groups as $groupClass) {
                /**@var  Model|TaskUserGroupInterface $group */
                $group = app($groupClass);

                $query
                    ->where('user_group_type', $groupClass)
                    ->whereIn(
                        'user_group_id',
                        $group::getGroupsForUserQuery(auth()->user())->select($group->getTable() . '.id')
                    );
            }
        });
    }
}
