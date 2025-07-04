<?php

namespace Ffhs\FfhsTasks;

use Ffhs\FfhsTasks\Filament\Resources\TaskServers\TaskServerResource;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
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
            TaskServerResource::class,
        ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(\Ffhs\FfhsTasks\Facades\FfhsTasks::__('navigation.group'))
                    ->collapsible(false),
            ]);
    }

    public function boot(Panel $panel): void
    {
    }
}
