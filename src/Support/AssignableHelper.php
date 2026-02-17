<?php

namespace Ffhs\FfhsTasks\Support;

use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Ffhs\FfhsTasks\Models\Assignable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;

class AssignableHelper
{
    public static function models(): Collection
    {
        return config()->collection('ffhs-tasks.assignable_models');
    }

    public static function hasModels(): bool
    {
        return static::models()->isNotEmpty();
    }

    /**
     * @return Collection<int, Model&AssignableInterface>
     */
    public static function assignables(string|null $search = null): Collection
    {
        /** @var Collection<int, Model&AssignableInterface> */
        return static::models()->flatMap(function ($modelClass) use ($search) {
            /** @var class-string<AssignableInterface> $modelClass */
            return $modelClass::searchQuery($search)->get();
        });
    }

    /**
     * @return Collection<int, Model&AssignableInterface>
     */
    public static function assignablesForUser(User|null $user = null): Collection
    {
        $user ??= auth()->user();

        /** @var Collection<int, Model&AssignableInterface> */
        return static::models()->flatMap(function ($modelClass) use ($user) {
            /** @var class-string<AssignableInterface> $modelClass */
            return $modelClass::queryForUser($user)->get();
        });
    }

    public static function getMorphKey(Assignable|(Model&AssignableInterface) $assignable): string
    {
        if ($assignable instanceof Assignable) {
            return $assignable->assignable_type.':::'.$assignable->assignable_id;
        }

        return $assignable::class.':::'.$assignable->getKey();
    }
}
