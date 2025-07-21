<?php

namespace Ffhs\FfhsTasks;

use Ffhs\FfhsTasks\Filament\Resources\Tasks\TaskResource;
use Ffhs\FfhsTasks\Filament\Resources\TaskServers\TaskServerResource;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskServer;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;

class FfhsTasksPlugin implements Plugin
{
    protected bool $hasTaskServerResource = true;
    protected bool $hasTaskResource = true;
    protected bool $hasRemoteTasks = true;

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

    public function hasRemoteTasks(bool $hasRemoteTasks = false): static
    {
        $this->hasRemoteTasks = $hasRemoteTasks;
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

            if ($this->hasRemoteTasks) {

                $navServers = \Ffhs\FfhsTasks\Facades\FfhsTasks::taskServers();
                $navServers = $navServers
                    ->map(fn(TaskServer $server) => NavigationItem::make($server->title)
                        ->group(\Ffhs\FfhsTasks\Facades\FfhsTasks::__('navigation.group'))
                        ->url($panel->getUrl() . '/tasks/remote/' . $server->id)
                        ->parentItem(Task::__('resource.navigation-label'))
                    );
                $panel->navigationItems($navServers->toArray());
            }
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
