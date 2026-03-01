<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\Assign;

use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

use function Ffhs\FfhsTasks\resolve_model_class;

final class AssignSelfAction
{
    public static function make(): Action
    {
        return Action::make('assign_me')
            ->closeModalByClickingAway(false)
            ->label(__('ffhs-tasks::actions.assign_me.label'))
            ->icon(Heroicon::OutlinedUserCircle)
            ->authorize('update', resolve_model_class(Task::class))
            ->visible(function (Task $record) {
                $user = auth()->user()?->withoutRelations();

                return $record->users->doesntContain($user) && $record->canBeEdited();
            })
            ->action(function (Task $record): void {
                $user = auth()->user();

                if (! $record->users->contains($user)) {
                    $record->users()->attach($user);
                }
            });
    }
}
