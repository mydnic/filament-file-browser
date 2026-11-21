<?php

use Illuminate\Http\UploadedFile;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mydnic\FilamentFileBrowser\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Build a real TemporaryUploadedFile the way Livewire does, so the upload paths
 * can be exercised without going through an HTTP upload request.
 */
function temporaryUpload(string $name, string $contents = 'contents'): TemporaryUploadedFile
{
    $disk = FileUploadConfiguration::disk();

    $storedPath = FileUploadConfiguration::storeTemporaryFile(
        UploadedFile::fake()->createWithContent($name, $contents),
        $disk
    );

    return TemporaryUploadedFile::createFromLivewire(basename($storedPath));
}
