<?php

namespace Mydnic\FilamentFileBrowser;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mydnic\FilamentFileBrowser\Services\FileBrowserService;

class FilamentFileBrowser
{
    /**
     * Get all files and directories in the specified path
     */
    public function getFilesAndDirectories(string $disk, string $path): array
    {
        return app(FileBrowserService::class)->getFilesAndDirectories($disk, $path);
    }

    /**
     * Upload a file to the specified path
     */
    public function uploadFile(string $disk, string $path, TemporaryUploadedFile $file): string
    {
        return app(FileBrowserService::class)->uploadFile($disk, $path, $file);
    }

    /**
     * Delete files or directories
     */
    public function deleteItems(string $disk, array $paths): void
    {
        app(FileBrowserService::class)->deleteItems($disk, $paths);
    }

    /**
     * Create a zip file from selected items
     */
    public function createZipFromItems(string $disk, array $paths): string
    {
        return app(FileBrowserService::class)->createZipFromItems($disk, $paths);
    }

    /**
     * Get available disks
     */
    public function getAvailableDisks(): array
    {
        $configuredDisks = config('filament-file-browser.disks', []);
        $allDisks = array_keys(config('filesystems.disks', []));

        return ! empty($configuredDisks)
            ? array_intersect($configuredDisks, $allDisks)
            : $allDisks;
    }
}
