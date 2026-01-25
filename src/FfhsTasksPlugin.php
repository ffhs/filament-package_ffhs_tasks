<?php

namespace Ffhs\FfhsTasks;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FfhsTasksPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-package_ffhs_tasks';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            TaskResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {

    }
}
