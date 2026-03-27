<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\Assign;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Components\AssignablesSelect;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Support\AssignableHelper;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

use function Ffhs\FfhsTasks\resolve_model_class;

final class AssignAction
{
    public static function make(): Action
    {
        return Action::make('assign')
            ->closeModalByClickingAway(false)
            ->label(__('ffhs-tasks::actions.assign.label'))
            ->icon(Heroicon::OutlinedUsers)
            ->modalWidth(Width::Large)
            ->authorize('update', resolve_model_class(Task::class))
            ->visible(fn (Task $record) => AssignableHelper::hasModels() && $record->canBeEdited())
            ->schema([
                AssignablesSelect::make('assignables'),
            ])
            ->fillForm(fn (Task $record) => [
                'assignables' => $record->assignables,
            ])
            ->action(function (): void {
                Notification::make()
                    ->success()
                    ->title('Assigned group')
                    ->send();
            });
    }
}
