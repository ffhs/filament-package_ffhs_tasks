<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables\TasksTable;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Scopes\IsActiveScope;
use Ffhs\FfhsTasks\Scopes\TaskGroupScope;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('ffhs-tasks::pages.index.title');
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::ScreenTwoExtraLarge;
    }

    public function table(Table $table): Table
    {
        return TasksTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->tap(new IsActiveScope()));
    }

    public function getTabs(): array
    {
        return [
            'my' => Tab::make()
                ->label(__('ffhs-tasks::pages.index.tabs.my'))
                ->modifyQueryUsing(function ($query) {
                    return $query->whereHas('users', function ($query) {
                        $query->where('users.id', auth()->id());
                    });
                }),

            'group' => Tab::make()
                ->label(__('ffhs-tasks::pages.index.tabs.groups'))
                ->modifyQueryUsing(function ($query) {
                    return $query->tap(new TaskGroupScope());
                }),

            'created' => Tab::make()
                ->label(__('ffhs-tasks::pages.index.tabs.created'))
                ->modifyQueryUsing(function ($query) {
                    $query
                        ->where('creator_type', auth()->user()::class)
                        ->where('creator_id', auth()->id());
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
                    ->whereIn(
                        'user_group_id',
                        $group::getGroupsForUserQuery(auth()->user())->select($group->getTable() . '.id')
                    );
            }
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
