<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks;

use App\Models\User;
use BackedEnum;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\CreateTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\EditTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListAllTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListArchivedTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables\TasksTable;
use Ffhs\FfhsTasks\Filament\Resources\TaskTags\Pages\ListTaskTags;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Scopes\IsActiveScope;
use Ffhs\FfhsTasks\Scopes\PrivacyScope;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

use function Ffhs\FfhsTasks\resolve_model_class;

class TaskResource extends Resource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    /**
     * @return class-string<Task>
     */
    public static function getModel(): string
    {
        return resolve_model_class(Task::class);
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('ffhs-tasks::tasks.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('ffhs-tasks::tasks.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('ffhs-tasks::tasks.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ffhs-tasks::tasks.plural_model_label');
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'archive' => ListArchivedTasks::route('/archive'),
            'all' => ListAllTasks::route('/all'),

            'create' => CreateTask::route('/create'),
            'edit' => EditTask::route('/{record}'),
            'handle' => HandleTask::route('/{record}/handle'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->tap(new PrivacyScope())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var User $user */
        $user = auth()->user();
        $tasksQuery = $user->tasks();

        return (string) $tasksQuery
            ->tap(new IsActiveScope())
            ->count();
    }

    public static function getNavigationItems(): array
    {
        // Filament doesn't recognize the page as a child item itself.
        return array_map(
            fn (NavigationItem $item) => $item->childItems([
                ...ListAllTasks::getNavigationItems(),
                ...ListArchivedTasks::getNavigationItems(),
                ...ListTaskTags::getNavigationItems(),
            ]),
            parent::getNavigationItems()
        );
    }
}
