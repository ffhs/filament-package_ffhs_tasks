<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\Assign;

use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

use function Ffhs\FfhsTasks\resolve_model_class;

final class UnassignSelfAction
{
    public static function make(): Action
    {
        $user = auth()->user()?->withoutRelations();

        return Action::make('unassign_me')
            ->closeModalByClickingAway(false)
            ->label(__('ffhs-tasks::actions.unassign_me.label'))
            ->icon(Heroicon::OutlinedUserMinus)
            ->authorize('update', resolve_model_class(Task::class))
            ->visible(fn (Task $record) => $record->users->contains($user) && $record->canBeEdited())
            ->action(function (Task $record): void {
                $user = auth()->user();

                $record->users()->detach($user);
            });
    }
}
