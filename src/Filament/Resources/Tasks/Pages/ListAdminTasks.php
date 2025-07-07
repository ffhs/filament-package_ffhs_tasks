<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables\TasksAdminTable;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListAdminTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    public function table(Table $table): Table
    {
        return TasksAdminTable::configure($table);
    }

    public function getTitle(): string|Htmlable
    {
        return Task::__('resource.pages.admin-index.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
