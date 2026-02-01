<?php

namespace Ffhs\FfhsTasks;

use Ffhs\FfhsTasks\Jobs\ExpireOverdueTasksJob;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Policies\TaskPolicy;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * @link https://github.com/spatie/laravel-package-tools Docs
 */
class FfhsTasksServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ffhs-tasks')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->discoversMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('ffhs/filament-package_ffhs_tasks');
            });
    }

    public function bootingPackage(): void
    {
        if (config('ffhs-tasks.run_migrations', true)) {
            $this->package->runsMigrations();
        }
    }

    public function packageBooted(): void
    {
        Gate::policy(Task::class, TaskPolicy::class);

        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->job(new ExpireOverdueTasksJob())->everyMinute();
        });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'ffhs/filament-package_ffhs_tasks';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            Css::make('filament-package_ffhs_tasks-styles', __DIR__ . '/../resources/css/index.css'),
        ];
    }
}
