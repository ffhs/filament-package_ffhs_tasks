<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Actions\Action;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Enums\Width;

final class CreateTaskAction
{
    public static function make(): Action
    {
        /** @var array<string, class-string<TaskType>> $types */
        $types = TaskType::getAllTypes();

        $creatableTypes = array_filter(
            $types,
            fn (string $type): bool => (new $type())->canBeCreatedViaUi()
        );

        $options = array_map(
            fn ($type) => $type::displayname(),
            $creatableTypes
        );

        if (count($options) <= 1) {
            return Action::make('create')
                ->label(fn (Action $action) => __('filament-actions::create.single.label', ['label' => $action->getModelLabel()]))
                ->url(
                    fn (array $data, Action $action) => TaskResource::getUrl('create', ['type' => key($options)])
                );
        }

        return Action::make('create')
            ->label(fn (Action $action) => __('filament-actions::create.single.label', ['label' => $action->getModelLabel()]))
            ->modalWidth(Width::Small)
            ->modalFooterActions([])
            ->schema([
                ToggleButtons::make('type')
                    ->hiddenLabel()
                    ->inline()
                    ->options($options)
                    ->extraInputAttributes([
                        '@click' => '$wire.callMountedAction()'
                    ])
            ])
            ->action(
                fn (array $data, Action $action) => $action->redirect(
                    TaskResource::getUrl('create', ['type' => $data['type']])
                )
            );
    }
}
