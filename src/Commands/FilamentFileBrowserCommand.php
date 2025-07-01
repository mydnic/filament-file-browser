<?php

namespace Mydnic\FilamentFileBrowser\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FilamentFileBrowserCommand extends Command
{
    public $signature = 'filament-file-browser:install';

    public $description = 'Install and configure the Filament File Browser plugin';

    public function handle(): int
    {
        $this->info('Installing Filament File Browser...');

        // Publish config
        $this->callSilently('vendor:publish', [
            '--tag' => 'filament-file-browser-config',
        ]);
        $this->info('✅ Configuration published');

        // Create temp directory for zip downloads if it doesn't exist
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
            $this->info('✅ Temporary directory created for zip downloads');
        }

        // Check if the public disk is configured
        if (!config('filesystems.disks.public')) {
            $this->warn('⚠️ Public disk not found in your filesystems configuration');
            $this->info('Make sure to configure your disks in config/filesystems.php');
        } else {
            $this->info('✅ Public disk found in your filesystems configuration');
        }

        // Create symbolic link for public disk if it doesn't exist
        if (!file_exists(public_path('storage'))) {
            $this->callSilently('storage:link');
            $this->info('✅ Storage symbolic link created');
        }

        $this->info('');
        $this->info('Filament File Browser has been installed successfully!');
        $this->info('');
        $this->info('To use the file browser in your panel, add the plugin to your panel provider:');
        $this->info('');
        $this->line('use Mydnic\FilamentFileBrowser\FilamentFileBrowserPlugin;');
        $this->line('');
        $this->line('public function panel(Panel $panel): Panel');
        $this->line('{');
        $this->line('    return $panel');
        $this->line('        // ... other configuration');
        $this->line('        ->plugins([');
        $this->line('            FilamentFileBrowserPlugin::make(),');
        $this->line('        ]);');
        $this->line('}');

        return self::SUCCESS;
    }
}
