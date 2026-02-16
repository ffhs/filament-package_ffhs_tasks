<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Components;

use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskUserGroup;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class UserGroupSelect
{
    public static function make(string $name): ?Select
    {
        return Select::make($name)
            ->label('User Groups')
            ->required()
            ->multiple()
            ->live()
            ->visible(config()->collection('ffhs-tasks.user_groups')->isNotEmpty())
            ->getSearchResultsUsing(fn (string $search): array => static::buildOptions($search)->all())
            ->getOptionLabelsUsing(fn (array $values): array => static::buildOptionLabels($values)->all())
            ->loadStateFromRelationshipsUsing(function (Select $component): void {
                $record = $component->getRecord();

                if (! $record instanceof Task) {
                    return;
                }

                /** @var EloquentCollection<int, TaskUserGroup> $pivots */
                $pivots = $record->taskUserGroups()->get();

                $state = $pivots
                    ->map(fn (TaskUserGroup $pivot): string => "{$pivot->user_group_type}:::{$pivot->user_group_id}")
                    ->all();

                $component->state($state);
            })
            ->saveRelationshipsUsing(function (Select $component, ?array $state): void {
                $record = $component->getRecord();

                if (! $record instanceof Task) {
                    return;
                }

                $record->taskUserGroups()->delete();

                if (empty($state)) {
                    return;
                }

                $pivotRecords = collect($state)
                    ->map(function (string $composite): array {
                        [$type, $id] = explode(':::', $composite, 2);

                        return [
                            'user_group_type' => $type,
                            'user_group_id' => $id,
                        ];
                    });

                $record->taskUserGroups()->createMany($pivotRecords->all());
            })
            ->dehydrateStateUsing(null);
    }

    /** @return Collection<string, string> */
    protected static function buildOptions(?string $search = null): Collection
    {
        /** @var Collection<int, class-string<TaskUserGroupInterface>> $userGroups */
        $userGroups = config()->collection('ffhs-tasks.user_groups');

        return $userGroups
            ->flatMap(function (string $groupClass) use ($search) {
                /** @var class-string<TaskUserGroupInterface> $groupClass */
                $groups = $groupClass::searchQuery($search)->get();

                return $groups->mapWithKeys(function (Model $group): array {
                    /** @var TaskUserGroupInterface&Model $group */
                    return ["{$group->getMorphClass()}:::{$group->getKey()}" => $group->displayName()];
                });
            });
    }

    /** @return Collection<string, string> */
    protected static function buildOptionLabels(array $values): Collection
    {
        return collect($values)
            ->groupBy(fn (string $composite): string => explode(':::', $composite, 2)[0])
            ->flatMap(function (Collection $items, string $morphType) {
                $ids = $items->map(fn (string $composite) => explode(':::', $composite, 2)[1]);

                /** @var class-string<Model&TaskUserGroupInterface> $modelClass */
                $modelClass = Relation::getMorphedModel($morphType) ?? $morphType;

                return $modelClass::query()
                    ->whereIn((new $modelClass())->getKeyName(), $ids)
                    ->get()
                    ->mapWithKeys(function (Model $group) {
                        /** @var TaskUserGroupInterface&Model $group */
                        return ["{$group->getMorphClass()}:::{$group->getKey()}" => $group->displayName()];
                    });
            });
    }
}
