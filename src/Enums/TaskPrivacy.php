<?php

namespace Ffhs\FfhsTasks\Enums;

use Filament\Support\Contracts\HasLabel;

enum TaskPrivacy: string implements HasLabel
{
    case Public = 'public';
    case Private = 'private';


    public function getLabel(): string
    {
        return match ($this) {
            static::Public => __('ffhs-tasks::enums.privacy.public'),
            static::Private => __('ffhs-tasks::enums.privacy.private'),
        };
    }

    public static function options(): array
    {
        return collect(static::cases())
            ->mapWithKeys(fn (TaskPrivacy $privacy) => [
                $privacy->value => $privacy->getLabel(),
            ])
            ->toArray();
    }
}
