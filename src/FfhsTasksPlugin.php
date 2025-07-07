<?php

namespace Ffhs\FfhsTasks;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Filament\Resources\TaskServers\TaskServerResource;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;

class FfhsTasksPlugin implements Plugin
{
    protected bool $hasTaskServerResource = true;
    protected bool $hasTaskResource = true;

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

    public function hasTaskServerResource(bool $hasTaskServerResource = false): static
    {
        $this->hasTaskServerResource = $hasTaskServerResource;
        return $this;
    }

    public function hasTaskResource(bool $hasTaskResource = false): static
    {
        $this->hasTaskResource = $hasTaskResource;
        return $this;
    }

    public function getId(): string
    {
        return 'filament-package_ffhs_tasks';
    }

    public function register(Panel $panel): void
    {
        if ($this->hasTaskServerResource) {
            $panel->resources([TaskServerResource::class]);
        }
        if ($this->hasTaskResource) {
            $panel->resources([TaskResource::class]);
        }

        $panel->navigationGroups([
            NavigationGroup::make()
                ->label(\Ffhs\FfhsTasks\Facades\FfhsTasks::__('navigation.group'))
                ->collapsible(false),
        ]);
    }

    public function boot(Panel $panel): void
    {
    }
}
