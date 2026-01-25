<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables\TasksTable;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Scopes\IsArchivedScope;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListArchivedTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('ffhs-tasks::pages.archive.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('ffhs-tasks::pages.archive.navigation_label');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('ffhs-tasks::pages.index.navigation_label');
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::ScreenTwoExtraLarge;
    }

    public function table(Table $table): Table
    {
        return TasksTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->tap(new IsArchivedScope()));
    }
}
