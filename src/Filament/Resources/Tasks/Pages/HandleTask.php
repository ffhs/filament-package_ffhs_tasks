<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\CancelTaskAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\CompleteTaskAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\ViewOrEditAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskForm;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

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
        return TaskForm::configure($schema, $this);
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
