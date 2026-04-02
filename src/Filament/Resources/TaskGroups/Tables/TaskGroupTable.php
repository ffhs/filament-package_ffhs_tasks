<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaskGroupTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('ffhs-tasks::task_groups.attributes.title'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('users_count')
                    ->label(__('ffhs-tasks::task_groups.attributes.users_count'))
                    ->counts('users')
                    ->sortable(),

                TextColumn::make('bulk_address')
                    ->label(__('ffhs-tasks::task_groups.attributes.bulk_address'))
            ]);
    }
}
