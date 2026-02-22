<?php

namespace App\Providers;

use App\Console\DbOpenCommand;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            DbOpenCommand::class,
        ]);

        Event::listen(CommandStarting::class, function ($event) {
            if (
                str_starts_with($event->command, 'boost:')
                || str_starts_with($event->command, 'make:livewire')
                || str_starts_with($event->command, 'make:filament')
            ) {
                app()->setBasePath(realpath(__DIR__.'/../../../'));
                app()->useAppPath(realpath(__DIR__.'/../../../src'));

                config()->set('boost.code_environments.claude_code.guidelines_path', base_path('CLAUDE.md'));
            }
        });
    }

    public function boot(): void
    {
        Route::middleware('web')
            ->group(__DIR__.'/../../routes/dev.php');
    }
}
