<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\HandleAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskForm;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    public string $type;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->title;
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
            HandleAction::make(),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canView($this->getRecord()), 403);
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
            $this->getCancelFormAction(),
            $this->getSaveFormAction()
                ->authorize('update'),
        ];
    }
}
