<?php

namespace Ffhs\FfhsTasks\Support;

use Exception;
use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Ffhs\FfhsTasks\Models\TaskUserGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;

class UserGroupsHelper
{
    public static function models(): Collection
    {
        return config()->collection('ffhs-tasks.user_groups');
    }

    public static function hasModels(): bool
    {
        return static::models()->isNotEmpty();
    }

    /**
     * @return Collection<int, Model&TaskUserGroupInterface>
     */
    public static function groups(string|null $search = null): Collection
    {
        /** @var Collection<int, Model&TaskUserGroupInterface> */
        return static::models()->flatMap(function ($modelClass) use ($search) {
            /** @var class-string<TaskUserGroupInterface> $modelClass */
            return $modelClass::searchQuery($search)->get();
        });
    }

    /**
     * @return Collection<int, Model&TaskUserGroupInterface>
     */
    public static function groupsForUser(User|null $user = null): Collection
    {
        $user ??= auth()->user();

        /** @var Collection<int, Model&TaskUserGroupInterface> */
        return static::models()->flatMap(function ($modelClass) use ($user) {
            /** @var class-string<TaskUserGroupInterface> $modelClass */
            return $modelClass::queryForUser($user)->get();
        });
    }

    public static function getMorphKey(TaskUserGroup|TaskUserGroupInterface $userGroup): string
    {
        if ($userGroup instanceof TaskUserGroup) {
            return $userGroup->user_group_type.':::'.$userGroup->user_group_id;
        }

        if ($userGroup instanceof TaskUserGroupInterface) {
            return $userGroup::class.':::'.$userGroup->getKey();
        }

        throw new Exception('Invalid user group type');
    }
}
