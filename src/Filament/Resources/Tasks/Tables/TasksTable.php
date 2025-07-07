<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\AssignActions;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['users', 'creator']))
            ->reorderable(false)
            ->columns([
                Split::make([
                    IconColumn::make('finished')
                        ->true(Heroicon::CheckBadge, Color::Gray)
                        ->false(Heroicon::Ticket, Color::Amber)
                        ->grow(false),
                    TextColumn::make('space')
                        ->grow(false)
                        ->state(''),
                    Stack::make([
                        TextColumn::make('title')
                            ->formatStateUsing(fn($state
                            ) => new HtmlString('<strong>' . htmlspecialchars($state) . '</strong>')),
                        TextColumn::make('description'),
                    ]),
                    TextColumn::make('type')
                        ->grow(false)
                        ->alignEnd()
                        ->formatStateUsing(function ($state) {
                            if (empty($state)) {
                                return null;
                            }
                            $taskType = TaskType::getTypeFromIdentifier($state);
                            return $taskType ? $taskType::displayname() : null;
                        }),
                    TextColumn::make('users.name')
                        ->label(Task::__('relations.users.label'))
                        ->alignEnd(),
                ])
            ])
            ->recordUrl(fn($record) => TaskResource::getUrl('handle', ['record' => $record]))
            ->recordActions([
                AssignActions::make()
//                ViewAction::make(),
//                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
