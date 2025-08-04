<?php

namespace Mydnic\FilamentFileBrowser\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mydnic\FilamentFileBrowser\Services\FileBrowserService;

class FileBrowserPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static string $view = 'filament-file-browser::pages.file-browser';

    protected static ?string $navigationLabel = 'File Browser';

    protected static ?string $title = 'File Browser';

    protected static ?string $slug = 'file-browser';

    public ?string $disk = null;

    public ?string $path = '/';

    public array $files = [];

    public array $breadcrumbs = [];

    public array $selectedItems = [];

    public ?array $data = [];

    public function mount(): void
    {
        $this->disk = config('filament-file-browser.default_disk', 'public');
        $this->form->fill([
            'disk' => $this->disk,
        ]);
        $this->loadFiles();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('disk')
                    ->label('Filesystem Disk')
                    ->options($this->getAvailableDisks())
                    ->live()
                    ->afterStateUpdated(fn (string $state) => $this->changeDisk($state)),

                FileUpload::make('files')
                    ->label('Upload Files')
                    ->multiple()
                    ->disk('local') // Temporary storage
                    ->directory('temp-uploads')
                    ->visibility('private')
                    ->acceptedFileTypes(['*'])
                    ->maxFiles(10)
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if (! empty($state)) {
                            $this->uploadFiles($state);
                            // Clear the field after upload
                            $this->form->fill(['files' => []]);
                        }
                    }),
            ])
            ->statePath('data');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-file-browser.navigation_group', 'Files');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-file-browser.navigation_sort', 0);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload')
                ->label('Upload Files')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('files')
                        ->label('Select Files')
                        ->multiple()
                        ->required()
                        ->disk($this->disk)
                        ->directory($this->path)
                        ->visibility('private'),
                ])
                ->action(function (array $data): void {
                    $this->uploadFiles($data['files']);
                }),

            Action::make('navigate_up')
                ->label('Up')
                ->icon('heroicon-o-arrow-up')
                ->visible(fn (): bool => $this->path !== '/')
                ->action(function (): void {
                    $this->navigateUp();
                }),
        ];
    }

    public function getAvailableDisks(): array
    {
        $disks = [];
        foreach (config('filesystems.disks', []) as $name => $config) {
            $disks[$name] = ucfirst($name);
        }

        return $disks;
    }

    public function loadFiles(): void
    {
        $service = app(FileBrowserService::class);

        // Add debugging
        Log::debug('Loading files from disk: ' . $this->disk . ', path: ' . $this->path);

        $this->files = $service->getFilesAndDirectories($this->disk, $this->path);

        // Debug the loaded files
        Log::debug('Loaded ' . count($this->files) . ' files/folders');
        foreach ($this->files as $file) {
            Log::debug('File: ' . $file['name'] . ' (type: ' . $file['type'] . ', path: ' . $file['path'] . ')');
        }

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

    public function uploadFiles(array $files): void
    {
        $service = app(FileBrowserService::class);

        foreach ($files as $file) {
            if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $service->uploadFile($this->disk, $this->path, $file);
            }
        }

        $this->loadFiles();

        Notification::make()
            ->title('Files uploaded successfully')
            ->success()
            ->send();
    }

    public function toggleSelect(string $path): void
    {
        if (in_array($path, $this->selectedItems)) {
            $this->selectedItems = array_filter($this->selectedItems, fn ($item) => $item !== $path);
        } else {
            $this->selectedItems[] = $path;
        }
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

        foreach ($this->selectedItems as $path) {
            $service->deleteItem($this->disk, $path);
        }

        $this->selectedItems = [];
        $this->loadFiles();

        Notification::make()
            ->title('Selected items deleted successfully')
            ->success()
            ->send();
    }

    public function downloadSelected(): void
    {
        if (empty($this->selectedItems)) {
            Notification::make()
                ->title('No items selected')
                ->warning()
                ->send();

            return;
        }

        if (count($this->selectedItems) === 1) {
            // Single file download
            $path = $this->selectedItems[0];
            $this->downloadSingleFile($path);

            return;
        }

        // Multiple files - create ZIP and trigger download
        $this->downloadMultipleFiles();
    }

    protected function downloadSingleFile(string $path): void
    {
        try {
            $url = Storage::disk($this->disk)->url($path);

            // Use a simpler approach without complex JavaScript
            //            $this->js("window.open(" . json_encode($url) . ", '_blank');");

            Notification::make()
                ->title('Download started')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Download failed')
                ->body('Could not download the file: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function downloadMultipleFiles(): void
    {
        try {
            $service = app(FileBrowserService::class);
            $zipPath = $service->createZipFromFiles($this->disk, $this->selectedItems);

            // For now, just show a notification that ZIP is ready
            // In a real implementation, you'd need a proper download route
            Notification::make()
                ->title('ZIP file created')
                ->body('Multiple file download feature needs a download route to be implemented')
                ->warning()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Download failed')
                ->body('Could not create ZIP file: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function openInNewTab(string $path): void
    {
        try {
            $url = Storage::disk($this->disk)->url($path);
            //            $this->js("window.open(" . json_encode($url) . ", '_blank');");
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error opening file')
                ->body('Could not open the file: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function downloadFile(string $path): void
    {
        $this->selectedItems = [$path];
        $this->downloadSelected();
    }

    public function deleteFile(string $path): void
    {
        $this->selectedItems = [$path];
        $this->deleteSelected();
    }
}
