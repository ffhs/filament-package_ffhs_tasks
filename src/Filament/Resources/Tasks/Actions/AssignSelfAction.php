<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class AssignSelfAction
{
    public static function make(): Action
    {
        return Action::make('assign_me')
            ->closeModalByClickingAway(false)
            ->label(__('ffhs-tasks::actions.assign_me.label'))
            ->icon(Heroicon::OutlinedUserCircle)
            ->authorize('update', Task::class)
            ->visible(function (Task $record) {
                $user = auth()->user()?->withoutRelations();

                return $record->users->doesntContain($user);
            })
            ->action(function (Task $record): void {
                $user = auth()->user();

                if (! $record->users->contains($user)) {
                    $record->users()->attach($user);
                }
            });
    }
}
