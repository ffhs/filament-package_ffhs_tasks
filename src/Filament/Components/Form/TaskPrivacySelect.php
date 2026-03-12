<?php

namespace Ffhs\FfhsTasks\Filament\Components\Form;

use Ffhs\FfhsTasks\Enums\TaskPrivacy;
use Filament\Forms\Components\Select;

class TaskPrivacySelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('ffhs-tasks::tasks.attributes.privacy'))
            ->required()
            ->selectablePlaceholder(false)
            ->default(TaskPrivacy::Public)
            ->enum(TaskPrivacy::class)
            ->options(TaskPrivacy::options());
    }


}
