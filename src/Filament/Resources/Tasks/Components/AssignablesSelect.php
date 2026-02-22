<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Components;

use Ffhs\FfhsTasks\Actions\SendTaskNotification;
use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskAssignedNotification;
use Ffhs\FfhsTasks\Support\AssignableHelper;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AssignablesSelect
{
    public static function make(string $name): ?Select
    {
        return Select::make($name)
            ->label(__('ffhs-tasks::tasks.attributes.assignables'))
            ->multiple()
            ->visible(AssignableHelper::hasModels())
            ->getSearchResultsUsing(fn (string $search): array => static::buildOptions($search)->all())
            ->getOptionLabelsUsing(fn (array $values): array => static::buildOptionLabels($values)->all())
            ->loadStateFromRelationshipsUsing(function (Select $component) use ($name): void {
                $record = $component->getRecord();

                if (! $record instanceof Task) {
                    return;
                }

                /** @var EloquentCollection<int, Assignable> $pivots */

                $pivots = $record->{$name}()->get();

                $state = $pivots
                    ->map(AssignableHelper::getCompositeKey(...))
                    ->all();

                $component->state($state);
            })
            ->saveRelationshipsUsing(function (Select $component, ?array $state) use ($name): void {
                $record = $component->getRecord();

                if (! $record instanceof Task) {
                    return;
                }

                $existingKeys = $record->{$name}()->get()
                    ->map(AssignableHelper::getCompositeKey(...))
                    ->toArray();

                $record->{$name}()->delete();

                if (empty($state)) {
                    return;
                }

                $pivotRecords = collect($state)
                    ->map(function (string $composite): array {
                        ['type' => $type, 'id' => $id] = AssignableHelper::parseCompositeKey($composite);

                        return [
                            'assignable_type' => $type,
                            'assignable_id' => $id,
                        ];
                    });

                $record->{$name}()->createMany($pivotRecords->all());

                $newKeys = collect($state)->diff($existingKeys);

                if ($newKeys->isNotEmpty()) {
                    static::notifyNewAssignables($record, $newKeys);
                }
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
                    return [AssignableHelper::getCompositeKey($group) => $group->displayName()];
                });
            });
    }

    /**
     * @param  Collection<int, string>  $compositeKeys
     */
    protected static function notifyNewAssignables(Task $task, Collection $compositeKeys): void
    {
        if (! in_array(TaskAssignedNotification::class, config('ffhs-tasks.notifications.enabled', []))) {
            return;
        }

        $sender = app(SendTaskNotification::class);
        $notification = new TaskAssignedNotification($task);
        $excludeActor = auth()->user();

        foreach (AssignableHelper::getModelsByComposite($compositeKeys) as $model) {
            $sender->notifyModel($model, $notification, $excludeActor);
        }
    }

    /** @return Collection<string, string> */
    protected static function buildOptionLabels(array $values): Collection
    {
        return AssignableHelper::getModelsByComposite(collect($values))
            ->mapWithKeys(function (Model $model): array {
                /** @var AssignableInterface&Model $model */
                return [AssignableHelper::getCompositeKey($model) => $model->displayName()];
            });
    }
}
