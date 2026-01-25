<?php

namespace Ffhs\FfhsTasks\Filament\Actions;

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;

final class ArchiveTaskAction
{
    public function make(): Action
    {
        return Action::make('archive')
            ->label('Archive')
            ->requiresConfirmation()
            ->action(function (Task $record) {
                $record->status = TaskStatus::Completed;
            });
    }
}
