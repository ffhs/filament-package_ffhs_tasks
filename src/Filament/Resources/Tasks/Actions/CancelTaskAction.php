<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

final class CancelTaskAction
{
    public static function make(): Action
    {
        return Action::make('cancel')
            ->label(__('ffhs-tasks::actions.cancel.label'))
            ->requiresConfirmation()
            ->color(Color::Red)
            ->icon(Heroicon::OutlinedXCircle)
            ->outlined()
            ->visible(fn (Task $record) => $record->can_be_cancelled)
            ->action(function (Task $record) {
                $record->cancel();

                Notification::make()
                    ->title(__('ffhs-tasks::actions.cancel.notification.title'))
                    ->body(__('ffhs-tasks::actions.cancel.notification.body'))
                    ->success()
                    ->send();

                return redirect()->to(TaskResource::getUrl('index'));
            });
    }
}
