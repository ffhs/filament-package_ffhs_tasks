<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes;

use Closure;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Forms\Components\TextInput;

class ValidationTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'validation';
    }

    public static function displayname(): string
    {
        return 'Validation Task';
    }

    public function getHandleComponents(): array|Closure
    {
        return [
            TextInput::make('required_field')
                ->required(),
        ];
    }
}
