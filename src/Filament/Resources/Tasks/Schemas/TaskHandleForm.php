<?php

namespace Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas;


use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Models\Task;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskHandleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make(Task::__('attributes.description.label'))
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                    ]),
                Section::make(Task::__('relations.users.label'))
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('users')
                            ->state(function ($record) {
                                $name = FfhsTasks::config('user.name_attribute');
                                return $record->users->pluck($name);
                            })
                            ->bulleted()
                            ->hiddenLabel()
                    ]),

                Section::make()
                    ->statePath('data')
                    ->columnSpanFull()
                    ->schema(fn(Task $record) => once(fn() => $record->getType()->getHandleSchema()))
            ]);
    }

}
