<?php

use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use Mydnic\FilamentFileBrowser\Services\FileBrowserService;

beforeEach(function () {
    Storage::fake('public');
});

it('lists directories before files, each group sorted alphabetically', function () {
    Storage::disk('public')->put('beta.txt', 'beta');
    Storage::disk('public')->put('alpha.txt', 'alpha');
    Storage::disk('public')->put('zeta/nested.txt', 'nested');
    Storage::disk('public')->put('Archive/nested.txt', 'nested');

    $items = app(FileBrowserService::class)->getFilesAndDirectories('public', '/');

    expect(array_column($items, 'name'))->toBe(['Archive', 'zeta', 'alpha.txt', 'beta.txt'])
        ->and(array_column($items, 'type'))->toBe(['dir', 'dir', 'file', 'file']);
});

it('lists the contents of a sub directory only', function () {
    Storage::disk('public')->put('root.txt', 'root');
    Storage::disk('public')->put('docs/guide.md', 'guide');
    Storage::disk('public')->put('docs/images/logo.png', 'png');

    $items = app(FileBrowserService::class)->getFilesAndDirectories('public', '/docs');

    expect(array_column($items, 'name'))->toBe(['images', 'guide.md']);
});

it('exposes size, mime type and url for files but not for directories', function () {
    Storage::disk('public')->put('notes.txt', 'hello');
    Storage::disk('public')->put('docs/guide.md', 'guide');

    $items = collect(app(FileBrowserService::class)->getFilesAndDirectories('public', '/'))
        ->keyBy('name');

    expect($items['notes.txt'])->toHaveKeys(['size', 'mime', 'full_url'])
        ->and($items['notes.txt']['size'])->toBe(5)
        ->and($items['docs'])->not->toHaveKey('size');
});

it('refuses to list a path that climbs out of the disk root', function () {
    Storage::disk('public')->put('inside.txt', 'inside');

    app(FileBrowserService::class)->getFilesAndDirectories('public', '../');
})->throws(FilesystemException::class);

it('stores an uploaded file under the current path', function () {
    $path = app(FileBrowserService::class)
        ->uploadFile('public', '/docs', temporaryUpload('report.txt', 'content'));

    expect($path)->toBe('docs/report.txt');
    Storage::disk('public')->assertExists('docs/report.txt');
    expect(Storage::disk('public')->get('docs/report.txt'))->toBe('content');
});

it('suffixes an uploaded file instead of overwriting an existing one', function () {
    Storage::disk('public')->put('report.txt', 'first');

    $path = app(FileBrowserService::class)
        ->uploadFile('public', '/', temporaryUpload('report.txt', 'second'));

    expect(basename($path))->toBe('report (1).txt');
    expect(Storage::disk('public')->get('report.txt'))->toBe('first');
    expect(Storage::disk('public')->get('report (1).txt'))->toBe('second');
});

it('deletes a single file and leaves the rest alone', function () {
    Storage::disk('public')->put('a.txt', 'a');
    Storage::disk('public')->put('b.txt', 'b');

    app(FileBrowserService::class)->deleteItem('public', 'a.txt');

    Storage::disk('public')->assertMissing('a.txt');
    Storage::disk('public')->assertExists('b.txt');
});

it('deletes a directory with everything inside it', function () {
    Storage::disk('public')->put('docs/guide.md', 'guide');
    Storage::disk('public')->put('docs/images/logo.png', 'png');
    Storage::disk('public')->put('keep.txt', 'keep');

    app(FileBrowserService::class)->deleteItems('public', ['docs']);

    Storage::disk('public')->assertMissing('docs/guide.md');
    Storage::disk('public')->assertMissing('docs/images/logo.png');
    Storage::disk('public')->assertExists('keep.txt');
});

it('ignores paths that do not exist when deleting', function () {
    Storage::disk('public')->put('a.txt', 'a');

    app(FileBrowserService::class)->deleteItems('public', ['nope.txt', 'a.txt']);

    Storage::disk('public')->assertMissing('a.txt');
});

it('zips the selected files and directories', function () {
    Storage::disk('public')->put('a.txt', 'a');
    Storage::disk('public')->put('docs/guide.md', 'guide');

    $zipPath = app(FileBrowserService::class)->createZipFromItems('public', ['a.txt', 'docs']);

    $zip = new ZipArchive;
    expect($zip->open($zipPath))->toBeTrue();

    $names = [];

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $names[] = $zip->getNameIndex($i);
    }

    $zip->close();
    unlink($zipPath);

    expect($names)->toContain('a.txt')
        ->and($names)->toContain('guide.md');
});
