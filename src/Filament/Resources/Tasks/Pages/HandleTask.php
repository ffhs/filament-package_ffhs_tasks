<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\CancelTaskAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\CompleteTaskAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\ViewOrEditAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class HandleTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    public string $type;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->title;
    }

    public function getBreadcrumb(): string
    {
        return __('ffhs-tasks::pages.handle.breadcrumb');
    }

    public function mount(string|int $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->type = $this->record->type;

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('title')
                    ->label(__('ffhs-tasks::tasks.attributes.title'))
                    ->disabled()
                    ->columnSpanFull()
                    ->required(),

                Grid::make()->columns(3)->components([
                    Group::make()
                        ->columnSpan(2)
                        ->columns(1)
                        ->components([
                            Section::make()
                                ->compact()
                                ->disabled()
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
                                    ->disabled()
                                    ->hiddenWhenAllChildComponentsHidden()
                                    ->schema(function (HandleTask $livewire, Section $component) {
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
                                    ->schema(function (HandleTask $livewire, Section $component) {
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
                        ->disabled()
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
                                        ->visible(function (HandleTask $livewire) {
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
                                        ->visible(function (HandleTask $livewire) {
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
                                    ->statePath('extra')
                                    ->schema(function (HandleTask $livewire, Section $component) {
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

    protected function getHeaderActions(): array
    {
        return [
            ViewOrEditAction::make(),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(auth()->user()?->can('handle', $this->getRecord()), 403);

        /** @var Task $record */
        $record = $this->getRecord();

        if ($record->isArchived()) {
            $this->redirect($this::$resource::getUrl());
        }

        if (! $record->getType()->canHandleTask($record)) {
            $this->redirect($this::$resource::getUrl());
        }
    }

    protected function getTaskType(): TaskType
    {
        /** @var Task $record */
        $record = $this->getRecord();

        return $record->getType();
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $record = $this->getRecord();
    //     $taskType = $this->getTaskType();
    //
    //     $data['cancelled'] = $this->cancelled;
    //     $data['finished'] = $this->finished;
    //
    //     if ($this->cancelled) {
    //         return $taskType->mutateDataBeforeCancel($record, $data);
    //     }
    //     if ($this->finished) {
    //         return $taskType->mutateDataBeforeFinish($record, $data);
    //     }
    //
    //     return $taskType->mutateDataBeforeSave($record, $data);
    // }

    protected function getFormActions(): array
    {
        return [
            CancelTaskAction::make()
                ->extraAttributes(['style' => 'margin-right: auto']),

            ActionGroup::make([
                CompleteTaskAction::make(),
                $this->getSaveFormAction(),
            ])->buttonGroup()
        ];
    }
}
