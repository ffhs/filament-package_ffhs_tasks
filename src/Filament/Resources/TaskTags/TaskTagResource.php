<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskTags;

use Ffhs\FfhsTasks\Filament\Resources\TaskTags\Pages\ListTaskTags;
use Ffhs\FfhsTasks\Filament\Resources\TaskTags\Schemas\TaskTagForm;
use Ffhs\FfhsTasks\Filament\Resources\TaskTags\Tables\TaskTagsTable;
use Ffhs\FfhsTasks\Models\TaskTag;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Ffhs\FfhsTasks\resolve_model_class;

class TaskTagResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function getModel(): string
    {
        return resolve_model_class(TaskTag::class);
    }

    public static function getModelLabel(): string
    {
        return __('ffhs-tasks::tags.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ffhs-tasks::tags.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return TaskTagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskTagsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskTags::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
