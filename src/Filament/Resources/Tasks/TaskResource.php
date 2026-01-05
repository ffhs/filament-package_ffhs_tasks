<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks;

use App\Models\User;
use BackedEnum;
use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\CreateTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListAdminTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListRemoteTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListTasksArchive;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskInfolist;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables\TasksTable;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Traits\IsTaskResource;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    use IsTaskResource;

    protected static ?string $model = Task::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentCheck;

    public static function infolist(Schema $schema): Schema
    {
        return TaskInfolist::configure($schema);
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
            'admin-index' => ListAdminTasks::route('/admin'),
            'create' => CreateTask::route('/create'),
            'handle' => HandleTask::route('/{record}'),
            'index-remote' => ListRemoteTasks::route('/remote/{server}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        /**@var User $user */
        $user = auth()->user();
        /**@phpstan-ignore-next-line */
        return FfhsTasks::modifyQueryActiveTask($user->tasks())->count() > 0;
    }
}
