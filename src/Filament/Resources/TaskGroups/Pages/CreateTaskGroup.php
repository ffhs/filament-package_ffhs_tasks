<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Pages;

use Ffhs\FfhsTasks\Filament\Resources\TaskGroups\TaskGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaskGroup extends CreateRecord
{
    protected static string $resource = TaskGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
