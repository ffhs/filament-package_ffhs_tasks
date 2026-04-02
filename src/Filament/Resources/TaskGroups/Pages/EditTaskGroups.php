<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Pages;

use Ffhs\FfhsTasks\Filament\Resources\TaskGroups\TaskGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaskGroups extends EditRecord
{
    protected static string $resource = TaskGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
        ];
    }
}
