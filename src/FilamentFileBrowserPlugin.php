<?php

namespace Mydnic\FilamentFileBrowser;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Mydnic\FilamentFileBrowser\Pages\FileBrowserPage;

class FilamentFileBrowserPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-file-browser';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                FileBrowserPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

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
}
