<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class UnassignSelfAction
{
    public static function make(): Action
    {
        $user = auth()->user()?->withoutRelations();

        return Action::make('unassign_me')
            ->closeModalByClickingAway(false)
            ->label(__('ffhs-tasks::actions.unassign_me.label'))
            ->icon(Heroicon::OutlinedUserMinus)
            ->authorize('update', Task::class)
            ->visible(fn (Task $record) => once(fn () => $record->users->contains($user)))
            ->action(function (Task $record): void {
                $user = auth()->user();

                $record->users()->detach($user);
            });
    }
}
