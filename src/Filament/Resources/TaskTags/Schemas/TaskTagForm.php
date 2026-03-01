<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskTags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaskTagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('display_name')
                    ->label(__('ffhs-tasks::tags.attributes.display_name'))
                    ->columnSpanFull()
                    ->unique(ignoreRecord: true),
            ]);
    }
}
