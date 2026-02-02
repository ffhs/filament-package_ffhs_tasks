<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\HandleAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskForm;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
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

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    public function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema, $this);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Task $task */
        $task = $this->getRecord();
        $taskType = $task->getType();

        if ($taskType) {
            return $taskType->mutateDataBeforeSave($task, $data);
        }

        return $data;
    }

    public function afterSave(): void
    {
        /** @var Task $task */
        $task = $this->getRecord();
        $task->getType()?->afterSave($task);
    }

    protected function getHeaderActions(): array
    {
        return [
            HandleAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
            $this->getSaveFormAction()
                ->authorize('update')
        ];
    }
}
