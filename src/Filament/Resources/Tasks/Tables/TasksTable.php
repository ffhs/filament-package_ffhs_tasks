<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Tables;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\HandleAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\ViewOrEditAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\ListTasks;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->with(['users', 'creator'])
            )
            ->defaultSort(
                fn (Builder $query) => $query
                    ->orderBy('deadline_at')
                    ->orderByDesc('created_at')
            )
            ->searchable(['description'])

            ->columns([
                TextColumn::make('id')
                    ->label(__('ffhs-tasks::tasks.attributes.id'))
                    ->badge()
                    ->visible(app()->isLocal()),

                TextColumn::make('type')
                    ->label(__('ffhs-tasks::tasks.attributes.type'))
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => TaskType::getTypeIdentifierNameList()[$state] ?? null)
                    ->searchable(query: function (Builder $query, string $search) {
                        $matchingTypes = collect(TaskType::getTypeIdentifierNameList())
                            ->filter(fn ($label) => str_contains(strtolower($label), strtolower($search)))
                            ->keys()
                            ->toArray();

                        $query->whereIn('type', $matchingTypes);
                    }),

                TextColumn::make('status')
                    ->label(__('ffhs-tasks::tasks.attributes.status'))
                    ->badge()
                    ->sortable()
                    ->visible(fn (Page $livewire) => ! $livewire instanceof ListTasks),


                TextColumn::make('title')
                    ->label(__('ffhs-tasks::tasks.attributes.title'))
                    ->searchable(),

                TextColumn::make('users.name')
                    ->label(__('ffhs-tasks::tasks.attributes.users'))
                    ->toggleable(),

                TextColumn::make('taskUserGroups.name')
                    ->label(__('ffhs-tasks::tasks.attributes.groups'))
                    ->toggleable()
                    ->expandableLimitedList(),

                TextColumn::make('creator.name')
                    ->label(__('ffhs-tasks::tasks.attributes.creator'))
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('starts_at')
                    ->label(__('ffhs-tasks::tasks.attributes.starts_at'))
                    ->toggleable()
                    ->dateTime('d.m.Y'),

                TextColumn::make('deadline_at')
                    ->label(__('ffhs-tasks::tasks.attributes.deadline_at'))
                    ->toggleable()
                    ->dateTime('d.m.Y'),
            ])
            ->recordClasses(fn (Task $record) => [
                $record->starts_at?->isFuture() ? 'opacity-50' : ''
            ])
            ->recordUrl(function (Task $record) {
                return $record->isArchived() ? null : TaskResource::getUrl('handle', ['record' => $record]);
            })
            ->recordActions([
                ActionGroup::make([
                    // Assign Self
                    // AssignUser (in Group)
                    // AssignGroup
                ]),
                // ViewAction::make()
                //     ->iconButton(),
                //
                HandleAction::make()->iconButton(),
                ViewOrEditAction::make()->iconButton(),
            ])
            ->toolbarActions([]);
    }
}
