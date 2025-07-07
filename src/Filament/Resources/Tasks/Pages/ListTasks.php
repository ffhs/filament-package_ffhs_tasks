<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    public function getTitle(): string|Htmlable
    {
        return Task::__('resource.pages.index.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('to_admin_side')
                ->link()
                ->label(Task::__('actions.to_admin_side.label'))
                ->tooltip(Task::__('actions.to_admin_side.tool_tip'))
                ->url(TaskResource::getUrl('admin-index')),
            
            CreateAction::make(),
        ];
    }
}
