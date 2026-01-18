<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskServers\Tables;

use Ffhs\FfhsTasks\Filament\Resources\TaskServers\Actions\TestTaskServerConnectionAction;
use Ffhs\FfhsTasks\Models\TaskServer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaskServersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->label(TaskServer::__('attributes.title.label')),

                TextColumn::make('url')
                    ->searchable()
                    ->label(TaskServer::__('attributes.url.label')),

                TextColumn::make('created_at')
                    ->label(TaskServer::__('attributes.created_at.label')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                TestTaskServerConnectionAction::make()
                    ->label(TaskServer::__('actions.test_connection.short_label')),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
