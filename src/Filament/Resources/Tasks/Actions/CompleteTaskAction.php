<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

final class CompleteTaskAction
{
    public static function make(): Action
    {
        return Action::make('complete')
            ->label(__('ffhs-tasks::actions.complete.label'))
            ->requiresConfirmation()
            ->color('primary')
            ->icon(Heroicon::CheckCircle)
            ->action(function (Task $record, HandleTask $livewire, array $data) {
                DB::beginTransaction();
                $record->complete();
                $record->update($livewire->form->getState());
                DB::commit();

                $type = $record->getType();
                $type?->afterComplete($record, $data);

                Notification::make()
                    ->title(__('ffhs-tasks::actions.complete.notification.title'))
                    ->body(__('ffhs-tasks::actions.complete.notification.body'))
                    ->success()
                    ->send();

                return redirect()->to(TaskResource::getUrl('index'));
            });
    }
}
