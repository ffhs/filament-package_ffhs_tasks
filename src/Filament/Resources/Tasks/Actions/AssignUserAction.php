<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Components\UserSelect;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

final class AssignUserAction
{
    public static function make(): Action
    {
        return Action::make('assign_user')
            ->closeModalByClickingAway(false)
            ->label(__('ffhs-tasks::actions.assign_user.label'))
            ->icon(Heroicon::OutlinedUser)
            ->modalWidth(Width::Large)
            ->authorize('update', Task::class)
            ->hidden(fn (Task $record) => $record->isArchived())
            ->schema([
                UserSelect::make('users'),
            ])
            ->action(function (): void {
                Notification::make()
                    ->success()
                    ->title('Assigned user');
            });
    }
}
