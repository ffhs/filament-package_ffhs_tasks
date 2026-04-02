<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskGroups;

use BackedEnum;
use Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Pages\CreateTaskGroup;
use Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Pages\EditTaskGroups;
use Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Pages\ListTaskGroups;
use Ffhs\FfhsTasks\Filament\Resources\TaskGroups\RelationManagers\TaskGroupUserRelation;
use Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Schemas\TaskGroupForm;
use Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Tables\TaskGroupTable;
use Ffhs\FfhsTasks\Models\TaskGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

use function Ffhs\FfhsTasks\resolve_model_class;

class TaskGroupResource extends Resource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    /**
     * @return class-string<TaskGroup>
     */
    public static function getModel(): string
    {
        return resolve_model_class(TaskGroup::class);
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('ffhs-tasks::task_groups.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('ffhs-tasks::task_groups.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('ffhs-tasks::task_groups.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ffhs-tasks::task_groups.plural_model_label');
    }

    public static function table(Table $table): Table
    {
        return TaskGroupTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return TaskGroupForm::configure($schema);
    }


    public static function getRelations(): array
    {
        return [
            TaskGroupUserRelation::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskGroups::route('/'),
            'create' => CreateTaskGroup::route('/create'),
            'edit' => EditTaskGroups::route('/{record}'),
        ];
    }

}
