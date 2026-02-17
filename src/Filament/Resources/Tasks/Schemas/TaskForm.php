<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Components\UserGroupSelect;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\EditTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class TaskForm
{
    public static function configure(Schema $schema, EditTask|HandleTask $livewire)
    {
        /** @var Task $record */
        $record = $livewire->record;
        $taskType = $record->getType();

        $canEdit = $taskType->canEditTask($record) && ! $livewire instanceof HandleTask;
        $canEditHandle = $livewire instanceof HandleTask;
        $canViewHandle = $canEditHandle || $record->isArchived();

        return $schema
            ->columns(1)
            ->components([
                TextInput::make('title')
                    ->label(__('ffhs-tasks::tasks.attributes.title'))
                    ->columnSpanFull()
                    ->disabled(! $canEdit)
                    ->required(),

                Grid::make()->columns(3)->components([
                    Group::make()
                        ->columnSpan(2)
                        ->columns(1)
                        ->components([
                            Section::make()
                                ->compact()
                                ->disabled(! $canEdit)
                                ->components([
                                    RichEditor::make('description')
                                        ->label(__('ffhs-tasks::tasks.attributes.description'))
                                        ->extraInputAttributes(['style' => '--rows: 3'])
                                        ->toolbarButtons([
                                            ['bold', 'italic', 'underline'],
                                            ['bulletList', 'orderedList'],
                                            ['undo', 'redo'],
                                        ]),
                                ]),

                                Section::make()
                                    ->compact()
                                    ->key('type-main-components')
                                    ->statePath('extra')
                                    ->hiddenWhenAllChildComponentsHidden()
                                    ->disabled(! $canEdit)
                                    ->schema(function (EditTask|HandleTask $livewire, Section $component) {
                                        if ($type = $livewire->type) {
                                            $taskType = TaskType::getTypeFromIdentifier($type);

                                            return $component->evaluate($taskType->getMainComponents());
                                        }

                                        return [];
                                    }),

                                Section::make()
                                    ->compact()
                                    ->key('type-handle-components')
                                    ->statePath('data')
                                    ->visible($canViewHandle)
                                    ->disabled(! $canEditHandle)
                                    ->hiddenWhenAllChildComponentsHidden()
                                    ->schema(function (EditTask|HandleTask $livewire, Section $component) {
                                        if ($type = $livewire->type) {
                                            $taskType = TaskType::getTypeFromIdentifier($type);

                                            return $component->evaluate($taskType->getHandleComponents());
                                        }

                                        return [];
                                    }),
                        ]),

                    Group::make()
                        ->columnSpan(1)
                        ->columns(1)
                        ->disabled(! $canEdit)
                        ->components([

                            Section::make()
                                ->compact()
                                ->columnSpan(1)
                                ->columns(1)
                                ->components([
                                    DateTimePicker::make('starts_at')
                                        ->label(__('ffhs-tasks::tasks.attributes.starts_at'))
                                        ->seconds(false)
                                        ->nullable()
                                        ->visible(function (EditTask|HandleTask $livewire) {
                                            if ($type = $livewire->type) {
                                                $taskType = TaskType::getTypeFromIdentifier($type);

                                                return $taskType->hasStartDate();
                                            }

                                            return false;
                                        }),

                                    DateTimePicker::make('deadline_at')
                                        ->label(__('ffhs-tasks::tasks.attributes.deadline_at'))
                                        ->seconds(false)
                                        ->nullable()
                                        ->visible(function (EditTask|HandleTask $livewire) {
                                            if ($type = $livewire->type) {
                                                $taskType = TaskType::getTypeFromIdentifier($type);

                                                return $taskType->hasDeadline();
                                            }

                                            return false;
                                        }),

                                    UserGroupSelect::make('taskUserGroups')
                                ]),

                                Section::make()
                                    ->compact()
                                    ->key('type-sidebar-components')
                                    ->statePath('extra')
                                    ->hiddenWhenAllChildComponentsHidden()
                                    ->schema(function (EditTask|HandleTask $livewire, Section $component) {
                                        if ($type = $livewire->type) {
                                            $taskType = TaskType::getTypeFromIdentifier($type);

                                            return $component->evaluate($taskType->getSidebarComponents());
                                        }

                                        return [];
                                    }),
                        ])
                ])
            ]);
    }
}
