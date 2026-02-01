<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\CreateTask;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskCreateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('title')
                    ->label(__('ffhs-tasks::tasks.attributes.title'))
                    ->columnSpanFull()
                    ->required(),

                Grid::make()->columns(3)->components([
                    Group::make()
                        ->columnSpan(2)
                        ->columns(1)
                        ->components([
                            Section::make()
                                ->compact()
                                ->columnSpan(2)
                                ->columns(1)
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
                                    ->statePath('data.extra')
                                    ->schema(function (CreateTask $livewire, Section $component) {
                                        if ($type = $livewire->type) {
                                            $taskType = TaskType::getTypeFromIdentifier($type);

                                            return $component->evaluate($taskType->getMainComponents());
                                        }

                                        return [];
                                    }),
                        ]),

                    Group::make()
                        ->columnSpan(1)
                        ->columns(1)
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
                                        ->visible(function (CreateTask $livewire) {
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
                                        ->visible(function (CreateTask $livewire) {
                                            if ($type = $livewire->type) {
                                                $taskType = TaskType::getTypeFromIdentifier($type);

                                                return $taskType->hasDeadline();
                                            }

                                            return false;
                                        }),

                                    Select::make('users')
                                        ->label(__('ffhs-tasks::tasks.attributes.users'))
                                        ->relationship('users', 'name')
                                        ->multiple(),
                                ]),

                                Section::make()
                                    ->compact()
                                    ->key('type-sidebar-components')
                                    ->statePath('data.extra')
                                    ->schema(function (CreateTask $livewire, Section $component) {
                                        if ($type = $livewire->type) {
                                            $taskType = TaskType::getTypeFromIdentifier($type);

                                            return $component->evaluate($taskType->getSidebarComponents());
                                        }

                                        return [];
                                    }),
                        ])
                ]),
            ]);
    }
}
