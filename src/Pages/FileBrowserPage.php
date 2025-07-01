<?php

namespace Mydnic\FilamentFileBrowser\Pages;

use Filament\Pages\Page;
use Mydnic\FilamentFileBrowser\Components\FileBrowser;

class FileBrowserPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static string $view = 'filament-file-browser::pages.file-browser';

    protected static ?string $navigationLabel = 'File Browser';

    protected static ?string $title = 'File Browser';

    protected static ?string $slug = 'file-browser';

    public static function getNavigationGroup(): ?string
    {
        return config('filament-file-browser.navigation_group', 'Files');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-file-browser.navigation_sort', 0);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FileBrowser::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
