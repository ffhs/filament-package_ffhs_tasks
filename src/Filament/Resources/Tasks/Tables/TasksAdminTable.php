<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables;

use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TasksAdminTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('title')
                    ->label(Task::__('attributes.title.label'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('finished')
                    ->label(Task::__('attributes.finished.label'))
                    ->boolean(),
                TextColumn::make('creator')
                    ->label(Task::__('attributes.creator.label'))
                    ->state(fn(Task $record) => $record->creator->displayCreatorName()),
                TextColumn::make('users.name')
                    ->label(Task::__('relations.users.label'))
                    ->searchable()
            ])
            ->modifyQueryUsing(function ($query) {
                $query->with('creator');
            })
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
