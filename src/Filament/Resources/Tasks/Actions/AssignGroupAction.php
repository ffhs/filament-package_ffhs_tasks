<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Components\UserGroupSelect;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Support\UserGroupsHelper;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

final class AssignGroupAction
{
    public static function make(): Action
    {
        return Action::make('assign_group')
            ->closeModalByClickingAway(false)
            ->label(__('ffhs-tasks::actions.assign_group.label'))
            ->icon(Heroicon::OutlinedUsers)
            ->modalWidth(Width::Large)
            ->authorize('update', Task::class)
            ->visible(fn (Task $record) => UserGroupsHelper::hasModels() && ! $record->isArchived())
            ->schema([
                UserGroupSelect::make('taskUserGroups'),
            ])
            ->fillForm(fn (Task $record) => [
                'taskUserGroups' => $record->taskUserGroups,
            ])
            ->action(function (): void {
                Notification::make()
                    ->success()
                    ->title('Assigned group');
            });
    }
}
