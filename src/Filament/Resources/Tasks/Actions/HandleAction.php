<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class HandleAction
{
    public static function make(): Action
    {
        return Action::make('handle')
            ->label(__('ffhs-tasks::actions.handle.label'))
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('gray')
            ->url(fn (Task $record) => TaskResource::getUrl('handle', ['record' => $record]))
            ->authorize(fn (Task $record) => $record->getType()->canHandleTask($record)
                && ($record->starts_at === null || $record->starts_at->isPast()));
    }
}
