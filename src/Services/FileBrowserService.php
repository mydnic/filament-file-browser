<?php

namespace Mydnic\FilamentFileBrowser\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use ZipArchive;

class FileBrowserService
{
    /**
     * Get all files and directories in the specified path
     */
    public function getFilesAndDirectories(string $disk, string $path): array
    {
        $path = $this->normalizePath($path);
        $storage = Storage::disk($disk);
        $contents = $storage->listContents($path, false);

        $directories = [];
        $files = [];

        foreach ($contents as $item) {
            $itemData = [
                'name' => basename($item['path']),
                'path' => $item['path'],
                'type' => $item['type'],
            ];

            if ($item['type'] === 'file') {
                $itemData['size'] = $storage->size($item['path']);
                $itemData['mime'] = $storage->mimeType($item['path']);
                $files[] = $itemData;
            } else {
                $directories[] = $itemData;
            }
        }

        // Sort directories and files alphabetically
        usort($directories, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
        usort($files, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        // Return directories first, then files
        return array_merge($directories, $files);
    }

    /**
     * Upload a file to the specified path
     */
    public function uploadFile(string $disk, string $path, TemporaryUploadedFile $file): string
    {
        $path = $this->normalizePath($path);
        $filename = $file->getClientOriginalName();
        $targetPath = trim($path, '/') . '/' . $filename;

        // Handle duplicate filenames by adding a suffix
        $storage = Storage::disk($disk);
        $counter = 1;
        $fileInfo = pathinfo($filename);
        $extension = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';
        $filenameWithoutExt = $fileInfo['filename'];

        while ($storage->exists($targetPath)) {
            $newFilename = $filenameWithoutExt . ' (' . $counter . ')' . $extension;
            $targetPath = trim($path, '/') . '/' . $newFilename;
            $counter++;
        }

        $file->storeAs($path, basename($targetPath), $disk);

        return $targetPath;
    }

    /**
     * Delete files or directories
     */
    public function deleteItems(string $disk, array $paths): void
    {
        $storage = Storage::disk($disk);

        foreach ($paths as $path) {
            if ($storage->exists($path)) {
                if ($this->isDirectory($disk, $path)) {
                    $storage->deleteDirectory($path);
                } else {
                    $storage->delete($path);
                }
            }
        }
    }

    /**
     * Delete a single item (file or directory)
     */
    public function deleteItem(string $disk, string $path): void
    {
        $this->deleteItems($disk, [$path]);
    }

    /**
     * Create a zip file from selected items
     */
    public function createZipFromItems(string $disk, array $paths): string
    {
        $storage = Storage::disk($disk);
        $tempPath = storage_path('app/temp');

        if (! file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        $zipFileName = 'download_' . Str::random(10) . '.zip';
        $zipFilePath = $tempPath . '/' . $zipFileName;

        $zip = new ZipArchive;
        $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($paths as $path) {
            if ($storage->exists($path)) {
                if ($this->isDirectory($disk, $path)) {
                    $this->addDirectoryToZip($zip, $storage, $path);
                } else {
                    $zip->addFromString(basename($path), $storage->get($path));
                }
            }
        }

        $zip->close();

        return $zipFilePath;
    }

    /**
     * Create a zip file from selected files (alias for compatibility)
     */
    public function createZipFromFiles(string $disk, array $paths): string
    {
        return $this->createZipFromItems($disk, $paths);
    }

    /**
     * Add a directory and its contents to a zip file
     *
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $storage
     */
    protected function addDirectoryToZip(ZipArchive $zip, $storage, string $path, string $zipPath = ''): void
    {
        $contents = $storage->listContents($path, false);

        foreach ($contents as $item) {
            $itemName = basename($item['path']);
            $itemZipPath = $zipPath ? $zipPath . '/' . $itemName : $itemName;

            if ($item['type'] === 'dir') {
                $zip->addEmptyDir($itemZipPath);
                $this->addDirectoryToZip($zip, $storage, $item['path'], $itemZipPath);
            } else {
                $zip->addFromString($itemZipPath, $storage->get($item['path']));
            }
        }
    }

    /**
     * Check if a path is a directory
     */
    protected function isDirectory(string $disk, string $path): bool
    {
        $storage = Storage::disk($disk);
        $metadata = collect($storage->listContents(dirname($path), false))
            ->where('path', $path)
            ->first();

        return $metadata && $metadata['type'] === 'dir';
    }

    /**
     * Normalize a path
     */
    protected function normalizePath(string $path): string
    {
        $path = trim($path, '/');

        return $path === '' ? '/' : $path;
    }
}
