<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskHandleForm;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Illuminate\Contracts\Support\Htmlable;

class HandleTask extends EditRecord
{
    protected static string $resource = TaskResource::class;
    protected bool $cancelled = false;

    public function form(Schema $schema): Schema
    {
        return TaskHandleForm::configure($schema);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecordTitle() . ': ' . $this->getRecord()->title;
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();
        /**@var Task $record */
        $record = $this->getRecord();
        if ($record->isArchived()) {
            $this->redirect($this::$resource::getUrl());
        }
    }

    protected function getTaskType(): TaskType
    {
        /**@var Task $record */
        $record = $this->getRecord();
        return $record->getType();
    }

    protected function cancel(): void
    {
        /**@var Task $record */
        $record = $this->getRecord();
        $taskType = $this->getTaskType();

        $this->cancelled = true;
        $this->save();

        $taskType->afterCancel($record, $this->form->getState());
    }

    protected function finish(): void
    {
        /**@var Task $record */
        $record = $this->getRecord();
        $taskType = $this->getTaskType();

        $this->save();

        $taskType->afterFinish($record, $this->form->getState());
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /**@var Task $record */
        $record = $this->getRecord();
        $taskType = $this->getTaskType();

        $data['cancelled'] = $this->cancelled;
        $data['finished'] = !$this->cancelled;
        return $this->cancelled
            ? $taskType->mutateDataBeforeCancel($record, $data)
            : $taskType->mutateDataBeforeFinish($record, $data);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCanceleFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('finish')
            ->label(Task::__('actions.finish.label'))
            ->submit('finish')
            ->action('finish')
            ->color(Color::Green);
    }

    protected function getCanceleFormAction(): Action
    {
        return Action::make('cancel')
            ->visible(fn() => $this->getRecord()->can_cancel)
            ->label(Task::__('actions.cancel.label'))
            ->submit('cancel')
            ->action('cancel')
            ->color('danger');
    }


}
