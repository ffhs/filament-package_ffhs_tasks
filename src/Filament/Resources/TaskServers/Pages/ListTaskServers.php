<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskServers\Pages;

use Ffhs\FfhsTasks\Filament\Resources\TaskServers\TaskServerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaskServers extends ListRecords
{
    protected static string $resource = TaskServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
