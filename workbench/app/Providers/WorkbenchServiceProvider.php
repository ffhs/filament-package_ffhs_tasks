<?php

namespace App\Providers;

use App\Console\DbOpenCommand;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            DbOpenCommand::class,
        ]);
    }

    public function boot(): void
    {
        //
    }
}
