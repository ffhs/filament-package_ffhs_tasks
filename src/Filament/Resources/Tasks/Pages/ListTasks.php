<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\CreateTaskAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables\TasksTable;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Scopes\AssigneeScope;
use Ffhs\FfhsTasks\Scopes\CreatorScope;
use Ffhs\FfhsTasks\Scopes\IsActiveScope;
use Ffhs\FfhsTasks\Scopes\TaskUserGroupScope;
use Ffhs\FfhsTasks\Support\UserGroupsHelper;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

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
                ->modifyQueryUsing(fn (Builder $query) => $query->tap(new AssigneeScope())),

            'group' => Tab::make()
                ->label(__('ffhs-tasks::pages.index.tabs.groups'))
                ->modifyQueryUsing(fn (Builder $query) => $query->tap(new TaskUserGroupScope()))
                ->visible(UserGroupsHelper::hasModels()),

            'created' => Tab::make()
                ->label(__('ffhs-tasks::pages.index.tabs.created'))
                ->modifyQueryUsing(fn (Builder $query) => $query->tap(new CreatorScope())),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateTaskAction::make(),
        ];
    }
}
