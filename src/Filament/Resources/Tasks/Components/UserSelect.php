<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Components;

use App\Models\User;
use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class UserSelect
{
    public static function make(string $name): Select
    {
        return Select::make($name)
            ->label(__('ffhs-tasks::tasks.attributes.users'))
            ->relationship('users', 'name')
            ->multiple()
            ->getSearchResultsUsing(function (string $search, Get $get): array {
                if (config()->collection('ffhs-tasks.user_groups')->isEmpty()) {
                    return User::pluck('name', 'id')->toArray();
                }

                $compositeValues = $get('taskUserGroups');

                if (empty($compositeValues)) {
                    return [];
                }

                return static::getUsersFromGroups($compositeValues, $search)
                    ->pluck('name', 'id')
                    ->all();
            });
    }

    /**
     * @param  array<int, string>  $compositeValues
     * @return Collection<int, Model>
     */
    protected static function getUsersFromGroups(array $compositeValues, ?string $search = null): Collection
    {
        return collect($compositeValues)
            ->groupBy(fn (string $composite): string => explode(':::', $composite, 2)[0])
            ->flatMap(function (Collection $items, string $morphType) use ($search) {
                $ids = $items->map(fn (string $composite) => explode(':::', $composite, 2)[1]);

                /** @var class-string<Model&TaskUserGroupInterface> $modelClass */
                $modelClass = Relation::getMorphedModel($morphType) ?? $morphType;

                /** @var Collection<int, Model&TaskUserGroupInterface> $groups */
                $groups = $modelClass::query()
                    ->whereIn((new $modelClass())->getKeyName(), $ids)
                    ->get();

                return $groups
                    ->flatMap(
                        fn (Model&TaskUserGroupInterface $group) => $group
                            ->usersQuery()
                            ->when($search, fn ($query, string $search) => $query->whereLike('name', "%$search%"))
                            ->get()
                    );
            })
            ->unique(fn (Model $model) => $model->getKey())
            ->values();
    }
}
