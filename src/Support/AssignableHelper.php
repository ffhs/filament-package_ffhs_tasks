<?php

namespace Ffhs\FfhsTasks\Support;

use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Models\Watchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
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

    /**
     * @param  Collection<int, string>  $compositeKeys
     * @return Collection<int, Model&AssignableInterface>
     */
    public static function getModelsByComposite(Collection $compositeKeys): Collection
    {
        return $compositeKeys
            ->groupBy(fn (string $composite): string => static::parseCompositeKey($composite)['type'])
            ->flatMap(function (Collection $items, string $morphType): Collection {
                $ids = $items->map(fn (string $composite) => static::parseCompositeKey($composite)['id']);

                /** @var class-string<Model&AssignableInterface> $modelClass */
                $modelClass = Relation::getMorphedModel($morphType) ?? $morphType;

                return $modelClass::query()
                    ->whereIn((new $modelClass())->getKeyName(), $ids)
                    ->get();
            });
    }

    /**
     * @return array{type: string, id: string}
     */
    public static function parseCompositeKey(string $compositeKey): array
    {
        [$type, $id] = explode(':::', $compositeKey, 2);

        return ['type' => $type, 'id' => $id];
    }

    public static function getCompositeKey(Assignable|Watchable|(Model&AssignableInterface) $assignable): string
    {
        if ($assignable instanceof Assignable || $assignable instanceof Watchable) {
            return $assignable->assignable_type.':::'.$assignable->assignable_id;
        }

        return $assignable->getMorphClass().':::'.$assignable->getKey();
    }
}
