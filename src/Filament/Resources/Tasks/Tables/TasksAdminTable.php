<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\AssignActions;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskUserGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
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
                    ->searchable()
                    ->sortable(),
                IconColumn::make('finished')
                    ->label(Task::__('attributes.finished.label'))
                    ->alignCenter()
                    ->searchable()
                    ->sortable()
                    ->boolean(),
                IconColumn::make('cancelled')
                    ->label(Task::__('attributes.cancelled.label'))
                    ->alignCenter()
                    ->searchable()
                    ->sortable()
                    ->boolean(),
                IconColumn::make('can_cancel')
                    ->label(Task::__('attributes.can_cancel.label'))
                    ->alignCenter()
                    ->searchable()
                    ->sortable()
                    ->boolean(),
                TextColumn::make('creator')
                    ->state(fn(Task $record) => $record->creator?->displayCreatorName())
                    ->label(Task::__('attributes.creator.label')),
                TextColumn::make('users.' . FfhsTasks::config('user.name_attribute'))
                    ->label(Task::__('relations.users.label'))
                    ->listWithLineBreaks()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('taskUserGroups')
                    ->label(Task::__('relations.taskUserGroups.label'))
                    ->formatStateUsing(function (TaskUserGroup $state) {
                        return $state->userGroup?->getGroupModelTitle();
                    })
                    ->listWithLineBreaks()
                    ->sortable()
            ])
            ->modifyQueryUsing(function ($query) {
                $query->with('creator', 'users', 'taskUserGroups.userGroup');
            })
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                AssignActions::make()
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
