<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;

final class CompleteTaskAction
{
    public static function make(): Action
    {
        return Action::make('complete')
            ->label(__('ffhs-tasks::actions.complete.label'))
            ->requiresConfirmation()
            ->color('primary')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->extraAttributes([
                "x-data" => "filamentFormButton",
                "x-bind:class" => "{ 'fi-processing': isProcessing }",
                "x-bind:disabled" => "isProcessing"
            ])
            ->mountUsing(function (HandleTask $livewire, Action $action) {
                try {
                    $livewire->form->validate();
                } catch (ValidationException $e) {
                    $errors = $e->validator->errors();
                    $livewire->setErrorBag($errors);

                    $action->cancel();
                }
            })
            ->action(function (Task $record, Action $action, HandleTask $livewire): void {
                $state = $livewire->form->getState();
                $record->complete($state);

                Notification::make()
                    ->title(__('ffhs-tasks::actions.complete.notification.title'))
                    ->body(__('ffhs-tasks::actions.complete.notification.body'))
                    ->success()
                    ->send();

                $livewire->redirect(TaskResource::getUrl('index'));
            });
    }
}
