<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

final class SaveWithoutValidationAction
{
    public static function make(): Action
    {
        return Action::make('save_without_validation')
            ->label('Save without validation')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
            ->keyBindings(['mod+s'])
            ->extraAttributes([
                "x-data" => "filamentFormButton",
                "x-bind:class" => "{ 'fi-processing': isProcessing }",
                "x-bind:disabled" => "isProcessing"
            ])
            ->action(function (Task $record, HandleTask $livewire) {
                $state = static::getFormStateWithoutValidation($livewire->form);

                $record->update($state);

                Notification::make()
                    ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                    ->success()
                    ->send();
            });
    }

    /**
     * Copied from HasState::getState() to remove validation
     */
    public static function getFormStateWithoutValidation(Schema $form): array
    {
        return Component::withVisibilityCache(function () use ($form): array {
            $state = [
                $form->getStatePath() => $form->getRawState(),
            ];

            $form->callBeforeStateDehydrated($state);

            $form->saveRelationships();
            $form->loadStateFromRelationships(shouldHydrate: true);

            $form->dehydrateState($state);
            $form->mutateDehydratedState($state);

            if ($statePath = $form->getStatePath()) {
                $state = data_get($state, $statePath) ?? [];
            }

            return $state;
        });
    }
}
