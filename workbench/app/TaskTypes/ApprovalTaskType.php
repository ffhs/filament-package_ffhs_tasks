<?php

namespace App\TaskTypes;

use Closure;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;

class ApprovalTaskType extends TaskType
{
    public static function identifier(): string
    {
        return 'approval';
    }

    public static function displayname(): string
    {
        return 'Approve';
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
            Textarea::make('approval_notes')
        ];
    }

    public function getSidebarComponents(): array|Closure
    {
        return function ($operation) {
            if ($operation === 'create') {
                return [
                    Toggle::make('requires_approval')
                        ->label('Requires Approval'),
                ];
            }

            return [
                IconEntry::make('requires_approval')
                    ->label('Requires Approval')
                    ->boolean(),
            ];
        };
    }

    public function getHandleComponents(): array|Closure
    {
        return [
            Toggle::make('is_approved')
                ->label('Approval'),

            Textarea::make('approval_comment')
                ->label('Comment')
                ->required(),
        ];
    }
}
