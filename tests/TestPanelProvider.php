<?php

namespace Mydnic\FilamentFileBrowser\Tests;

use Filament\Panel;
use Filament\PanelProvider;
use Mydnic\FilamentFileBrowser\FilamentFileBrowserPlugin;

/**
 * Minimal panel so the page can be rendered the way a host application would.
 */
class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('testing')
            ->path('testing')
            ->plugin(FilamentFileBrowserPlugin::make());
    }
}
