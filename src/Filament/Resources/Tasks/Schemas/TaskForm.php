<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas;

use Ffhs\FfhsTasks\Enums\TaskPrivacy;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Components\AssignablesSelect;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\CreateTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\EditTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\HandleTask;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class TaskForm
{
    protected static CreateTask|EditTask|HandleTask $livewire;

    protected static function getRecord(): ?Task
    {
        return once(function () {
            if (static::$livewire instanceof CreateTask) {
                return null;
            }

            /** @var Task $task */
            $task = static::$livewire->getRecord();

            return $task;
        });
    }

    protected static function getTaskType(): TaskType
    {
        return once(function () {
            if (static::$livewire instanceof CreateTask) {
                return TaskType::getTypeFromIdentifier(static::$livewire->type);
            }

            return static::getRecord()->getType();
        });
    }

    protected static function canEdit(): bool
    {
        return once(function () {
            if (static::$livewire instanceof CreateTask) {
                return true;
            }

            return static::getTaskType()->canEditTask(static::getRecord()) && ! static::$livewire instanceof HandleTask;
        });
    }

    protected static function canEditHandle(): bool
    {
        return once(function () {
            if (static::$livewire instanceof CreateTask) {
                return false;
            }

            return static::$livewire instanceof HandleTask;
        });
    }

    protected static function canViewHandle(): bool
    {
        return once(function () {
            if (static::$livewire instanceof CreateTask) {
                return false;
            }

            return static::canEditHandle() || static::getRecord()->isArchived();
        });
    }

    public static function configure(Schema $schema, CreateTask|EditTask|HandleTask $livewire): Schema
    {
        static::$livewire = $livewire;

        return $schema
            ->columns(1)
            ->components([
                TextInput::make('title')
                    ->label(__('ffhs-tasks::tasks.attributes.title'))
                    ->columnSpanFull()
                    ->disabled(! static::canEdit())
                    ->required(),

                Grid::make()->columns(3)->components([
                    Group::make()
                        ->columnSpan(2)
                        ->columns(1)
                        ->components(static::getMainComponents()),

                    Group::make()
                        ->columnSpan(1)
                        ->columns(1)
                        ->disabled(! static::canEdit())
                        ->components(static::getSidebarComponents()),
                ]),
            ]);
    }

    protected static function getMainComponents(): array
    {
        return [
            Section::make()
                ->compact()
                ->disabled(! static::canEdit())
                ->components([
                    RichEditor::make('description')
                        ->label(__('ffhs-tasks::tasks.attributes.description'))
                        ->extraInputAttributes(['style' => '--rows: 3'])
                        ->required()
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
                ->disabled(! static::canEdit())
                ->schema(function (CreateTask|EditTask|HandleTask $livewire, Section $component) {
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
                ->visible(static::canViewHandle())
                ->disabled(! static::canEditHandle())
                ->hiddenWhenAllChildComponentsHidden()
                ->schema(function (CreateTask|EditTask|HandleTask $livewire, Section $component) {
                    if ($type = $livewire->type) {
                        $taskType = TaskType::getTypeFromIdentifier($type);

                        return $component->evaluate($taskType->getHandleComponents());
                    }

                    return [];
                }),
        ];
    }

    protected static function getSidebarComponents(): array
    {
        return [
            Section::make()
                ->compact()
                ->columnSpan(1)
                ->columns(1)
                ->components([
                    DateTimePicker::make('starts_at')
                        ->label(__('ffhs-tasks::tasks.attributes.starts_at'))
                        ->seconds(false)
                        ->nullable()
                        ->visible(function (CreateTask|EditTask|HandleTask $livewire) {
                            $taskType = static::getTaskType();

                            return $taskType->hasStartDate();
                        }),

                    DateTimePicker::make('deadline_at')
                        ->label(__('ffhs-tasks::tasks.attributes.deadline_at'))
                        ->seconds(false)
                        ->nullable()
                        ->visible(function (CreateTask|EditTask|HandleTask $livewire) {
                            $taskType = static::getTaskType();

                            return $taskType->hasDeadline();
                        }),

                    Select::make('privacy')
                        ->label(__('ffhs-tasks::tasks.attributes.privacy'))
                        ->required()
                        ->selectablePlaceholder(false)
                        ->default(TaskPrivacy::Public)
                        ->enum(TaskPrivacy::class)
                        ->options(TaskPrivacy::options()),

                    AssignablesSelect::make('assignables')
                        ->required(),

                    AssignablesSelect::make('watchables')
                        ->label(__('ffhs-tasks::tasks.attributes.watchables')),

                    Toggle::make('can_be_cancelled')
                        ->label(__('ffhs-tasks::tasks.attributes.can_be_cancelled'))
                        ->required()
                        ->visible(function (CreateTask|HandleTask|EditTask $livewire) {
                            if (! $livewire instanceof CreateTask) {
                                return false;
                            }

                            $taskType = static::getTaskType();

                            return $taskType->canBeCancelled();
                        }),
                ]),

                Section::make()
                    ->compact()
                    ->key('type-sidebar-components')
                    ->statePath('extra')
                    ->hiddenWhenAllChildComponentsHidden()
                    ->schema(function (CreateTask|EditTask|HandleTask $livewire, Section $component) {
                        $taskType = static::getTaskType();

                        return $component->evaluate($taskType->getSidebarComponents());
                    }),
        ];
    }
}
