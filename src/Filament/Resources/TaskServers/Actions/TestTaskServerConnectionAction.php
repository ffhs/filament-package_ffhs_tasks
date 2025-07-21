<?php

namespace Ffhs\FfhsTasks\Filament\Resources\TaskServers\Actions;

use Ffhs\FfhsTasks\Models\TaskServer;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;

class TestTaskServerConnectionAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'test_connection';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this
            ->tooltip(TaskServer::__('actions.test_connection.tool_tip'))
            ->label(TaskServer::__('actions.test_connection.label'))
            ->icon(Heroicon::ArrowDownCircle)
            ->action(fn() => Log::info('ToDo')); //ToDo implement Action
    }


}
