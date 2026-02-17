<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Components\AssignablesSelect;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    #[Url]
    public ?string $type = null;

    public function form(Schema $schema): Schema
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
                                    ->statePath('extra')
                                    ->hiddenWhenAllChildComponentsHidden()
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

                                    Toggle::make('can_be_cancelled')
                                        ->label(__('ffhs-tasks::tasks.attributes.can_be_cancelled'))
                                        ->visible(function (CreateTask $livewire) {
                                            if ($type = $livewire->type) {
                                                $taskType = TaskType::getTypeFromIdentifier($type);

                                                return $taskType->canBeCancelled();
                                            }

                                            return false;
                                        }),

                                    AssignablesSelect::make('assignables'),
                                ]),

                                Section::make()
                                    ->compact()
                                    ->key('type-sidebar-components')
                                    ->statePath('extra')
                                    ->hiddenWhenAllChildComponentsHidden()
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = $this->type;

        return $data;
    }
}
