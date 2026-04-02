<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\CancelTaskAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\CompleteTaskAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\SaveWithoutValidationAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Actions\ViewOrEditAction;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskGroupForm;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Override;

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

    protected function authorizeAccess(): void
    {
        abort_unless(auth()->user()?->can('handle', $this->getRecord()), 403);

        /** @var Task $record */
        $record = $this->getRecord();

        if ($record->isArchived()) {
            $this->redirect($this::$resource::getUrl());
        }

        if (!$record->getType()->canHandleTask($record)) {
            $this->redirect($this::$resource::getUrl());
        }
    }

    public function form(Schema $schema): Schema
    {
        return TaskGroupForm::configure($schema, $this);
    }

    /**
     * Overridden to add "novalidate" attribute
     */
    #[Override]
    public function getFormContentComponent(): Component
    {
        if (!$this->hasFormWrapper()) {
            return Group::make([
                EmbeddedSchema::make('form'),
                $this->getFormActionsContentComponent(),
            ]);
        }

        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->extraAttributes(['novalidate' => 'true'])
            ->livewireSubmitHandler($this->getSubmitFormLivewireMethodName())
            ->footer([
                $this->getFormActionsContentComponent(),
            ]);
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
            ViewOrEditAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            CancelTaskAction::make(),

            ActionGroup::make([
                CompleteTaskAction::make(),
                SaveWithoutValidationAction::make(),
            ])
                ->extraAttributes(['style' => 'margin-left: auto'])
                ->buttonGroup(),
        ];
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
}
