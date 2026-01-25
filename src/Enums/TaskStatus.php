<?php

namespace Ffhs\FfhsTasks\Enums;

use BackedEnum;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum TaskStatus: string implements HasLabel, HasIcon, HasColor
{
    case InProgress  = 'in-progress';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';
    case Expired   = 'expired';

    public function getLabel(): string|Htmlable
    {
        return match ($this) {
            self::InProgress => __('In Progress'),
            self::Completed => __('Completed'),
            self::Cancelled => __('Cancelled'),
            self::Expired => __('Expired')
        };
    }

    public function getColor(): string|array
    {
        return match ($this) {
            self::InProgress => Color::Blue,
            self::Completed => Color::Green,
            self::Cancelled => Color::Slate,
            self::Expired => Color::Red
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable
    {
        return match ($this) {
            self::InProgress => Heroicon::Clock,
            self::Completed => Heroicon::CheckCircle,
            self::Cancelled => Heroicon::XCircle,
            self::Expired => Heroicon::ExclamationCircle,
        };
    }
}
