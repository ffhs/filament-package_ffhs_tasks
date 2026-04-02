<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskForm;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskGroupForm;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    #[Url]
    public string $type;

    public function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema, $this);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = $this->type;

        return $data;
    }
}
