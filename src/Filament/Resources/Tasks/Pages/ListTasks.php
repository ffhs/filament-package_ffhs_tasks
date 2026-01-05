<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
                        })
                        ->orderBy('created_at');
                }),
            'group' => Tab::make()
                ->label(Task::__('resource.pages.index.tabs.groups'))
                ->badge(function ($query) {
                    return $this->modifyGroupTaskQuery(FfhsTasks::modifyQueryActiveTask(Task::query()))->count();
                })
                ->modifyQueryUsing(function ($query) {
                    return $this->modifyGroupTaskQuery(FfhsTasks::modifyQueryActiveTask($query));
                }),
            'created' => Tab::make()
                ->label(Task::__('resource.pages.index.tabs.created'))
                ->modifyQueryUsing(function ($query) {
                    $query->where('creator_type', auth()->user()::class)
                        ->where('creator_id', auth()->id())
                        ->orderByDesc('created_at');
                }),
            'archive' => Tab::make()
                ->label(Task::__('resource.pages.index.tabs.archive'))
                ->modifyQueryUsing(function ($query) {
                    return FfhsTasks::modifyQueryArchiveTasks($query)
                        ->whereHas('users', function ($query) {
                            $query->where('users.id', auth()->id());
                        })
                        ->orderByDesc('created_at');
                }),
        ];
    }

    protected function modifyGroupTaskQuery(Builder $query): Builder
    {
        return $query->whereHas('taskUserGroups', function (Builder $query) {
            $groups = FfhsTasks::userGroups();
            foreach ($groups as $groupClass) {
                /**@var  Model|TaskUserGroupInterface $group */
                $group = app($groupClass);
                $query->where('user_group_type', $groupClass)
                    ->whereIn('user_group_id',
                        $group::getGroupsForUserQuery(auth()->user())->select($group->getTable() . '.id'));
            }
        });
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
