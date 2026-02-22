<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes;

use Closure;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class CreateTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'create-task';
    }

    public static function displayname(): string
    {
        return 'Create Task';
    }

    public function hasStartDate(): bool
    {
        return true;
    }

    public function hasDeadline(): bool
    {
        return true;
    }

    public function canBeCancelled(): bool
    {
        return true;
    }

    public function getMainComponents(): array|Closure
    {
        return [
            TextInput::make('reason')
                ->required(),
        ];
    }

    public function getSidebarComponents(): array|Closure
    {
        return [
            Toggle::make('is_urgent')
                ->label('Urgent'),
        ];
    }
}
