<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas;

use Ffhs\FfhsTasks\Models\Task;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskCreateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(Task::__('attributes.title.label'))
                            ->helperText(Task::__('attributes.title.helper_text'))
                            ->markAsRequired(false)
                            ->required(),
                        Select::make('type')
                            ->label(Task::__('attributes.type.label'))
                            ->label(Task::__('attributes.type.label'))
                            ->required()
                            ->options([
                                'test' => 'test'
                            ]),
                        Textarea::make('description')
                            ->helperText(Task::__('attributes.description.helper_text'))
                            ->label(Task::__('attributes.description.label'))
                            ->columnSpanFull()
                            ->nullable(),
                        Fieldset::make('Zeit')
                            ->columnSpanFull()
                            ->columns()
                            ->schema([
                                DateTimePicker::make('start_at')
                                    ->helperText(Task::__('attributes.start_at.helper_text'))
                                    ->label(Task::__('attributes.start_at.label'))
                                    ->seconds(false)
                                    ->nullable(),
                                DateTimePicker::make('deadline_at')
                                    ->helperText(Task::__('attributes.deadline_at.helper_text'))
                                    ->label(Task::__('attributes.deadline_at.label'))
                                    ->seconds(false)
                                    ->nullable(),
                            ])
                    ])
            ]);
    }
}
