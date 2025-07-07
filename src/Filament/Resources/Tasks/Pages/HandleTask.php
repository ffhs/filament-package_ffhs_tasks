<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Filament\Resources\Pages\EditRecord;

class HandleTask extends EditRecord
{
    protected static string $resource = TaskResource::class;


    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
