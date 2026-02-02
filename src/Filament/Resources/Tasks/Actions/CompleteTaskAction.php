<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

final class CompleteTaskAction
{
    public static function make(): Action
    {
        return Action::make('complete')
            ->label(__('ffhs-tasks::actions.complete.label'))
            ->requiresConfirmation()
            ->color('primary')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->action(function (Task $record, HandleTask $livewire): void {
                $record->complete($livewire->form->getState());

                Notification::make()
                    ->title(__('ffhs-tasks::actions.complete.notification.title'))
                    ->body(__('ffhs-tasks::actions.complete.notification.body'))
                    ->success()
                    ->send();

                $livewire->redirect(TaskResource::getUrl('index'));
            });
    }
}
