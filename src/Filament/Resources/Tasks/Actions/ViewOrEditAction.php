<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class ViewOrEditAction
{
    public static function make(): Action
    {
        return Action::make('view_or_edit')
            ->label(
                fn (Task $record) => static::canEdit($record)
                ? __('filament-actions::edit.single.label')
                : __('filament-actions::view.single.label')
            )
            ->color('gray')
            ->icon(fn (Task $record) => static::canEdit($record) ? Heroicon::OutlinedPencilSquare : Heroicon::OutlinedEye)
            ->url(fn (Task $record) => TaskResource::getUrl('edit', ['record' => $record]))
            ->visible(fn (Task $record) => $record->getType()->canViewTask($record));
    }

    public static function canEdit(Task $record): bool
    {
        if ($record->isArchived()) {
            return false;
        }

        return $record->getType()->canEditTask($record);
    }
}
