<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Components;

use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Support\AssignableHelper;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class AssignablesSelect
{
    public static function make(string $name): ?Select
    {
        return Select::make($name)
            ->label('Assigned To')
            ->required()
            ->multiple()
            ->live()
            ->visible(AssignableHelper::hasModels())
            ->getSearchResultsUsing(fn (string $search): array => static::buildOptions($search)->all())
            ->getOptionLabelsUsing(fn (array $values): array => static::buildOptionLabels($values)->all())
            ->loadStateFromRelationshipsUsing(function (Select $component): void {
                $record = $component->getRecord();

                if (! $record instanceof Task) {
                    return;
                }

                /** @var EloquentCollection<int, Assignable> $pivots */
                $pivots = $record->assignables()->get();

                $state = $pivots
                    ->map(AssignableHelper::getMorphKey(...))
                    ->all();

                $component->state($state);
            })
            ->saveRelationshipsUsing(function (Select $component, ?array $state): void {
                $record = $component->getRecord();

                if (! $record instanceof Task) {
                    return;
                }

                $record->assignables()->delete();

                if (empty($state)) {
                    return;
                }

                $pivotRecords = collect($state)
                    ->map(function (string $composite): array {
                        [$type, $id] = explode(':::', $composite, 2);

                        return [
                            'assignable_type' => $type,
                            'assignable_id' => $id,
                        ];
                    });

                $record->assignables()->createMany($pivotRecords->all());
            })
            ->dehydrateStateUsing(null);
    }

    /** @return Collection<string, string> */
    protected static function buildOptions(?string $search = null): Collection
    {
        /** @var Collection<int, class-string<AssignableInterface>> $userGroups */
        $userGroups = AssignableHelper::models();

        return $userGroups
            ->flatMap(function (string $groupClass) use ($search) {
                /** @var class-string<AssignableInterface> $groupClass */
                $groups = $groupClass::searchQuery($search)->get();

                return $groups->mapWithKeys(function (Model $group): array {
                    /** @var AssignableInterface&Model $group */
                    return [AssignableHelper::getMorphKey($group) => $group->displayName()];
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

                /** @var class-string<Model&AssignableInterface> $modelClass */
                $modelClass = Relation::getMorphedModel($morphType) ?? $morphType;

                return $modelClass::query()
                    ->whereIn((new $modelClass())->getKeyName(), $ids)
                    ->get()
                    ->mapWithKeys(function (Model $group) {
                        /** @var AssignableInterface&Model $group */
                        return [AssignableHelper::getMorphKey($group) => $group->displayName()];
                    });
            });
    }
}
