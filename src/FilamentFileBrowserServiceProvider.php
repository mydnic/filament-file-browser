<?php

namespace Mydnic\FilamentFileBrowser;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Mydnic\FilamentFileBrowser\Services\FileBrowserService;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentFileBrowserServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-file-browser';

    public static string $viewNamespace = 'filament-file-browser';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile('filament-file-browser')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('mydnic/filament-file-browser');
            });

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        // Register the FileBrowserService
        $this->app->singleton(FileBrowserService::class);
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-file-browser/{$file->getFilename()}"),
                ], 'filament-file-browser-stubs');
            }
        }
    }

    protected function getAssetPackageName(): ?string
    {
        return 'mydnic/filament-file-browser';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            Css::make('filament-file-browser-styles', __DIR__ . '/../resources/dist/filament-file-browser.css'),
            Js::make('filament-file-browser-scripts', __DIR__ . '/../resources/dist/filament-file-browser.js'),
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [
            'file-browser' => __DIR__ . '/../resources/icons/file-browser.svg',
        ];
    }
}
