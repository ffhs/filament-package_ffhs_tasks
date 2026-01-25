<?php

namespace Ffhs\FfhsTasks;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
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
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('ffhs/filament-package_ffhs_tasks');
            });
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );
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
