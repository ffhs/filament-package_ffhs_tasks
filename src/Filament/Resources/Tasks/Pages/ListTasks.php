<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    public function getTitle(): string|Htmlable
    {
        return Task::__('resource.pages.index.title');
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::ScreenLarge;
    }

    public function getTabs(): array
    {
        return [
            'my' => Tab::make()
                ->label(Task::__('resource.pages.index.tabs.my'))
                ->modifyQueryUsing(function ($query) {
                    return FfhsTasks::modifyQueryActiveTask($query)
                        ->whereHas('users', function ($query) {
                            $query->where('users.id', auth()->id());
                        });
                }),
            'created' => Tab::make()
                ->label(Task::__('resource.pages.index.tabs.created'))
                ->modifyQueryUsing(function ($query) {
                    $query->where('creator_type', auth()->user()::class)
                        ->where('creator_id', auth()->id());
                }),
            'archive' => Tab::make()
                ->label(Task::__('resource.pages.index.tabs.archive'))
                ->modifyQueryUsing(function ($query) {
                    return FfhsTasks::modifyQueryArchiveTasks($query)
                        ->whereHas('users', function ($query) {
                            $query->where('users.id', auth()->id());
                        });
                }),
        ];
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
