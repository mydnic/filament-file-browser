<?php

namespace Mydnic\FilamentFileBrowser;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Mydnic\FilamentFileBrowser\Commands\FilamentFileBrowserCommand;
use Mydnic\FilamentFileBrowser\Components\FileBrowser;
use Mydnic\FilamentFileBrowser\Testing\TestsFilamentFileBrowser;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentFileBrowserServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-file-browser';

    public static string $viewNamespace = 'filament-file-browser';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasConfigFile('filament-file-browser')
            ->hasCommands($this->getCommands())
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
        // Register the service
        $this->app->singleton(FileBrowser::class);

        // Register routes
        $this->registerRoutes();
    }

    public function packageBooted(): void
    {
        // Register Livewire components
        $this->registerLivewireComponents();

        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
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

        // Testing
        Testable::mixin(new TestsFilamentFileBrowser);
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => 'filament-file-browser',
            'middleware' => ['web', 'auth'],
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        });
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('filament-file-browser::file-browser', FileBrowser::class);
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
            // AlpineComponent::make('filament-file-browser', __DIR__ . '/../resources/dist/components/filament-file-browser.js'),
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

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_filament-file-browser_table',
        ];
    }
}
