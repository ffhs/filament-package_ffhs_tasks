<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskCreateForm;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    #[Url]
    public ?string $type = null;

    public function form(Schema $schema): Schema
    {
        return TaskCreateForm::configure($schema);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = $this->type;

        return $data;
    }
}
