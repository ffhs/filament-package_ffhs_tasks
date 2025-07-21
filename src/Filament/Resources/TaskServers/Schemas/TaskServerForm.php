<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskServers\Schemas;

use Ffhs\FfhsTasks\Filament\Resources\TaskServers\Actions\TestTaskServerConnectionAction;
use Ffhs\FfhsTasks\Models\TaskServer;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskServerForm
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
                            ->label(TaskServer::__('attributes.title.label'))
                            ->helperText(TaskServer::__('attributes.title.helper_text'))
                            ->markAsRequired(false)
                            ->required(),
                        Fieldset::make()
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('url')
                                    ->label(TaskServer::__('attributes.url.label'))
                                    ->helperText(TaskServer::__('attributes.url.helper_text'))
                                    ->markAsRequired(false)
                                    ->columnStart(1)
                                    ->required()
                                    ->url(),
                                TextInput::make('token')
                                    ->label(TaskServer::__('attributes.token.label'))
                                    ->helperText(TaskServer::__('attributes.token.helper_text'))
                                    ->markAsRequired(false)
                                    ->revealable()
                                    ->password()
                                    ->required(),

                                TestTaskServerConnectionAction::make()
                            ])
                    ])
            ]);
    }
}
