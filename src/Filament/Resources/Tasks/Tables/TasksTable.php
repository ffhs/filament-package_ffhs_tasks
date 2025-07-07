<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\AssignActions;
use Ffhs\FfhsTasks\Models\Task;
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
//                    ->label(Task::__('attributes.finished.label'))
                        ->label('')
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
                    TextColumn::make('users.name')
                        ->label(Task::__('relations.users.label'))
                        ->alignCenter(),
                ])
            ])
            ->recordActions([
                AssignActions::make()
//                ViewAction::make(),
//                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
