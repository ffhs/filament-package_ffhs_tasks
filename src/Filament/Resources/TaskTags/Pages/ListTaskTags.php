<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskTags\Pages;

use Ffhs\FfhsTasks\Filament\Resources\TaskTags\TaskTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListTaskTags extends ListRecords
{
    protected static string $resource = TaskTagResource::class;

    public static function getNavigationLabel(): string
    {
        return __('ffhs-tasks::tags.navigation_label');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->modalWidth(Width::Large),
        ];
    }
}
