<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
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
            ->columns(components: [
                Split::make([
                    self::getIconColumn()->grow(false),

                    Stack::make([
                        TextColumn::make('title')
                            ->searchable()
                            ->formatStateUsing(fn($state
                            ) => new HtmlString('<strong>' . htmlspecialchars($state) . '</strong>')),
                        TextColumn::make('description')->searchable(),
                    ])->grow(),

                    TextColumn::make('deadline_at')
                        ->label(Task::__('attributes.deadline_at.label'))
                        ->dateTime('d.m.Y H:i')
                        ->sortable()
                        ->alignRight()
                        ->grow(),

                    TextColumn::make('type')
                        ->label('Typ')
                        ->sortable()
                        ->alignCenter()
                        ->grow()
                        ->formatStateUsing(fn($state) => TaskType::getTypeIdentifierNameList()[$state] ?? null)
                        ->searchable(query: function (Builder $query, string $search) {
                            $matchingTypes = collect(TaskType::getTypeIdentifierNameList())
                                ->filter(fn($label) => str_contains(strtolower($label), strtolower($search)))
                                ->keys()
                                ->toArray();

                            $query->whereIn('type', $matchingTypes);
                        }),

                    Stack::make([
                        TextColumn::make('creator_type')
                            ->state(fn(Task $record
                            ) => new HtmlString('<strong>' . htmlspecialchars($record->creator?->displayCreatorName()) . '</strong>'))
                            ->label(Task::__('attributes.creator.label'))
                            ->sortable(),
                        TextColumn::make('users.' . FfhsTasks::config('user.name_attribute'))
                            ->label(Task::__('relations.users.label'))
                            ->sortable(),
                        //ToDo add groups
                    ])->grow()->alignEnd(),
                ])
            ])
            ->recordUrl(function (Task $record) {
                return $record->isArchived() ? null : TaskResource::getUrl('handle', ['record' => $record]);
            })
            ->recordActions([
                AssignActions::make()
                // ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    protected static function getIconColumn(): IconColumn
    {
        return IconColumn::make('finished')
            ->label(Task::__('attributes.state.label'))
            ->grow(false)
            ->sortable(query: function (Builder $query, string $direction) {
                $query->orderBy('finished', $direction)
                    ->orderBy('cancelled', $direction);
            })
            ->icon(function (Task $record) {
                if ($record->finished) {
                    return Heroicon::CheckBadge;
                }
                if ($record->cancelled) {
                    return Heroicon::XCircle;
                }
                return Heroicon::Ticket;
            })
            ->color(function (Task $record) {
                if ($record->finished) {
                    return Color::Gray;
                }
                if ($record->cancelled) {
                    return Color::Red;
                }
                return Color::Amber;
            })
            ->tooltip(function (Task $record) {
                if ($record->finished) {
                    return Task::__('attributes.finished.label');
                }
                if ($record->cancelled) {
                    return Task::__('attributes.cancelled.label');
                }
                return '';
            });
    }
}
