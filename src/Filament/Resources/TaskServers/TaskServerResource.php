<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskServers;

use BackedEnum;
use Ffhs\FfhsTasks\Filament\Resources\TaskServers\Pages\CreateTaskServer;
use Ffhs\FfhsTasks\Filament\Resources\TaskServers\Pages\ListTaskServers;
use Ffhs\FfhsTasks\Filament\Resources\TaskServers\Schemas\TaskServerForm;
use Ffhs\FfhsTasks\Filament\Resources\TaskServers\Tables\TaskServersTable;
use Ffhs\FfhsTasks\Models\TaskServer;
use Ffhs\FfhsTasks\Traits\IsTaskResource;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TaskServerResource extends Resource
{
    use IsTaskResource;

    protected static ?string $model = TaskServer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return TaskServerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskServersTable::configure($table);
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
            'index' => ListTaskServers::route('/'),
            'create' => CreateTaskServer::route('/create'),
        ];
    }
}
