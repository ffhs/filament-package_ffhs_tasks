<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskCreateForm;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    public function form(Schema $schema): Schema
    {
        return TaskCreateForm::configure($schema);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['creator_id'] = $user->id;
        $data['creator_type'] = $user::class;

        return $data;
    }
}
