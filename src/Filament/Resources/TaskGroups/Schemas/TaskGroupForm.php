<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('ffhs-tasks::task_groups.attributes.title'))
                            ->required(),
                        TextInput::make('bulk_address')
                            ->label(__('ffhs-tasks::task_groups.attributes.bulk_address'))
                            ->email(),
                    ])
            ]);
    }
}
