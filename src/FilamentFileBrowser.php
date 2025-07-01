<?php

namespace Mydnic\FilamentFileBrowser;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mydnic\FilamentFileBrowser\Services\FileBrowserService;

class FilamentFileBrowser 
{
    /**
     * Get all files and directories in the specified path
     *
     * @param string $disk
     * @param string $path
     * @return array
     */
    public function getFilesAndDirectories(string $disk, string $path): array
    {
        return app(FileBrowserService::class)->getFilesAndDirectories($disk, $path);
    }
    
    /**
     * Upload a file to the specified path
     *
     * @param string $disk
     * @param string $path
     * @param TemporaryUploadedFile $file
     * @return string
     */
    public function uploadFile(string $disk, string $path, TemporaryUploadedFile $file): string
    {
        return app(FileBrowserService::class)->uploadFile($disk, $path, $file);
    }
    
    /**
     * Delete files or directories
     *
     * @param string $disk
     * @param array $paths
     * @return void
     */
    public function deleteItems(string $disk, array $paths): void
    {
        app(FileBrowserService::class)->deleteItems($disk, $paths);
    }
    
    /**
     * Create a zip file from selected items
     *
     * @param string $disk
     * @param array $paths
     * @return string
     */
    public function createZipFromItems(string $disk, array $paths): string
    {
        return app(FileBrowserService::class)->createZipFromItems($disk, $paths);
    }
    
    /**
     * Get available disks
     *
     * @return array
     */
    public function getAvailableDisks(): array
    {
        $configuredDisks = config('filament-file-browser.disks', []);
        $allDisks = array_keys(config('filesystems.disks', []));
        
        return !empty($configuredDisks) 
            ? array_intersect($configuredDisks, $allDisks) 
            : $allDisks;
    }
}
