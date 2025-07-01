<?php

namespace Mydnic\FilamentFileBrowser\Components;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Mydnic\FilamentFileBrowser\Services\FileBrowserService;

class FileBrowser extends Widget
{
    use WithFileUploads;

    protected static string $view = 'filament-file-browser::components.file-browser';

    public ?string $disk = null;
    public ?string $path = null;
    public $files = [];
    public $uploadedFiles = [];
    public $selectedItems = [];
    public $breadcrumbs = [];

    protected $listeners = ['refreshFileBrowser' => '$refresh'];

    public function mount(): void
    {
        $this->disk = config('filament-file-browser.default_disk', 'public');
        $this->path = '/';
        $this->loadFiles();
    }

    public function loadFiles(): void
    {
        $service = app(FileBrowserService::class);
        $this->files = $service->getFilesAndDirectories($this->disk, $this->path);
        $this->updateBreadcrumbs();
    }

    public function updateBreadcrumbs(): void
    {
        $this->breadcrumbs = [];
        $paths = explode('/', trim($this->path, '/'));
        $currentPath = '';

        // Add root
        $this->breadcrumbs[] = [
            'name' => 'Root',
            'path' => '/',
        ];

        // Add intermediate directories
        foreach ($paths as $segment) {
            if (empty($segment)) {
                continue;
            }

            $currentPath .= "/{$segment}";
            $this->breadcrumbs[] = [
                'name' => $segment,
                'path' => $currentPath,
            ];
        }
    }

    public function navigateToFolder(string $path): void
    {
        $this->path = $path;
        $this->loadFiles();
    }

    public function navigateUp(): void
    {
        $parentPath = dirname($this->path);
        if ($parentPath === '.') {
            $parentPath = '/';
        }

        $this->navigateToFolder($parentPath);
    }

    public function changeDisk(string $disk): void
    {
        $this->disk = $disk;
        $this->path = '/';
        $this->loadFiles();
    }

    public function uploadFiles(): void
    {
        $this->validate([
            'uploadedFiles.*' => 'required|file',
        ]);

        $service = app(FileBrowserService::class);

        foreach ($this->uploadedFiles as $file) {
            /** @var TemporaryUploadedFile $file */
            $service->uploadFile($this->disk, $this->path, $file);
        }

        $this->uploadedFiles = [];
        $this->loadFiles();

        Notification::make()
            ->title('Files uploaded successfully')
            ->success()
            ->send();
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedItems)) {
            Notification::make()
                ->title('No items selected')
                ->warning()
                ->send();
            return;
        }

        $service = app(FileBrowserService::class);
        $service->deleteItems($this->disk, $this->selectedItems);

        $this->selectedItems = [];
        $this->loadFiles();

        Notification::make()
            ->title('Items deleted successfully')
            ->success()
            ->send();
    }

    public function downloadAsZip(): void
    {
        if (empty($this->selectedItems)) {
            Notification::make()
                ->title('No items selected')
                ->warning()
                ->send();
            return;
        }

        $service = app(FileBrowserService::class);
        $zipPath = $service->createZipFromItems($this->disk, $this->selectedItems);

        $this->redirect(route('filament-file-browser.download-zip', ['path' => $zipPath]));
    }

    public function toggleSelect($path): void
    {
        if (in_array($path, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$path]);
        } else {
            $this->selectedItems[] = $path;
        }
    }

    public function isSelected($path): bool
    {
        return in_array($path, $this->selectedItems);
    }

    public function deleteItem($path): void
    {
        $service = app(FileBrowserService::class);
        $service->deleteItems($this->disk, [$path]);
        $this->loadFiles();
        
        Notification::make()
            ->title('Item deleted successfully')
            ->success()
            ->send();
    }

    public function getAvailableDisks(): array
    {
        $disks = config('filesystems.disks');
        $options = [];
        
        foreach ($disks as $disk => $config) {
            $options[$disk] = $disk;
        }
        
        return $options;
    }

    protected function formatBytes($bytes, $precision = 2): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }

    public function render(): View
    {
        return view('filament-file-browser::components.file-browser');
    }
}
