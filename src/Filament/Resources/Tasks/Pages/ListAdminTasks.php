<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables\TasksAdminTable;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListAdminTasks extends ListTasks
{
    public function table(Table $table): Table
    {
        return TasksAdminTable::configure($table);
    }

    public function getTitle(): string|Htmlable
    {
        return Task::__('resource.pages.admin-index.title');
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::FitContent;
    }

    public function getTabs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
