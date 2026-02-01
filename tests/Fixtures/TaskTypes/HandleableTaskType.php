<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\TaskTypes;

use Closure;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class HandleableTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'handleable';
    }

    public static function displayname(): string
    {
        return 'Handleable Task';
    }

    public function hasStartDate(): bool
    {
        return true;
    }

    public function hasDeadline(): bool
    {
        return true;
    }

    public function getHandleComponents(): array|Closure
    {
        return [
            Toggle::make('handled'),
            Textarea::make('notes'),
        ];
    }
}
