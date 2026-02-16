<?php

namespace Ffhs\FfhsTasks\Support;

use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
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
}
