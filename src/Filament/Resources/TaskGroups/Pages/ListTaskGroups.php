<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Pages;

use Ffhs\FfhsTasks\Filament\Resources\TaskGroups\TaskGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListTaskGroups extends ListRecords
{
    protected static string $resource = TaskGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalWidth(Width::Large),
        ];
    }
}
