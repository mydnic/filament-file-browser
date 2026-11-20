<?php

use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Mydnic\FilamentFileBrowser\Pages\FileBrowserPage;

beforeEach(function () {
    Storage::fake('public');

    config()->set('filament-file-browser.default_disk', 'public');
});

it('renders and lists the contents of the default disk', function () {
    Storage::disk('public')->put('readme.txt', 'hello');
    Storage::disk('public')->put('docs/guide.md', 'guide');

    Livewire::test(FileBrowserPage::class)
        ->assertSet('disk', 'public')
        ->assertSet('path', '/')
        ->assertSee('readme.txt')
        ->assertSee('docs');
});

it('renders an empty state when the directory has no content', function () {
    Livewire::test(FileBrowserPage::class)
        ->assertSee('This directory is empty.');
});

it('navigates into a directory and back up to the root', function () {
    Storage::disk('public')->put('docs/guide.md', 'guide');

    Livewire::test(FileBrowserPage::class)
        ->call('navigateToFolderBase64', base64_encode('docs'))
        ->assertSet('path', 'docs')
        ->assertSet('breadcrumbs', [
            ['name' => 'Root', 'path' => '/'],
            ['name' => 'docs', 'path' => '/docs'],
        ])
        ->assertSee('guide.md')
        ->call('navigateUp')
        ->assertSet('path', '/')
        ->assertSee('docs');
});

it('falls back to the root when the encoded folder path is not valid base64', function () {
    Livewire::test(FileBrowserPage::class)
        ->call('navigateToFolderBase64', 'not base64!')
        ->assertSet('path', '/');
});

it('resets to the root of the disk it switches to', function () {
    Storage::fake('local');
    Storage::disk('local')->put('other.txt', 'other');
    Storage::disk('public')->put('docs/guide.md', 'guide');

    Livewire::test(FileBrowserPage::class)
        ->call('navigateToFolderBase64', base64_encode('docs'))
        ->call('changeDisk', 'local')
        ->assertSet('disk', 'local')
        ->assertSet('path', '/')
        ->assertSee('other.txt');
});

it('toggles an item in and out of the selection', function () {
    Livewire::test(FileBrowserPage::class)
        ->call('toggleSelect', 'a.txt')
        ->assertSet('selectedItems', ['a.txt'])
        ->call('toggleSelect', 'a.txt')
        ->assertSet('selectedItems', []);
});

it('deletes a file and drops it from the listing', function () {
    Storage::disk('public')->put('a.txt', 'a');
    Storage::disk('public')->put('b.txt', 'b');

    Livewire::test(FileBrowserPage::class)
        ->call('deleteFileBase64', base64_encode('a.txt'))
        ->assertSet('selectedItems', [])
        ->assertDontSee('a.txt')
        ->assertSee('b.txt');

    Storage::disk('public')->assertMissing('a.txt');
    Storage::disk('public')->assertExists('b.txt');
});

it('deletes nothing when the encoded path is not valid base64', function () {
    Storage::disk('public')->put('a.txt', 'a');

    Livewire::test(FileBrowserPage::class)
        ->call('deleteFileBase64', 'not base64!');

    Storage::disk('public')->assertExists('a.txt');
});

it('uploads files into the directory currently being browsed', function () {
    Storage::disk('public')->put('docs/guide.md', 'guide');

    $page = Livewire::test(FileBrowserPage::class)
        ->call('navigateToFolderBase64', base64_encode('docs'))
        ->instance();

    $page->uploadFiles([temporaryUpload('report.txt', 'content')]);

    Storage::disk('public')->assertExists('docs/report.txt');
    expect(Storage::disk('public')->get('docs/report.txt'))->toBe('content');
});

it('builds the upload action schema', function () {
    Livewire::test(FileBrowserPage::class)
        ->assertActionExists('upload')
        ->mountAction('upload')
        ->assertActionMounted('upload');
});

it('only offers the up action outside the root', function () {
    Storage::disk('public')->put('docs/guide.md', 'guide');

    Livewire::test(FileBrowserPage::class)
        ->assertActionHidden('navigate_up')
        ->call('navigateToFolderBase64', base64_encode('docs'))
        ->assertActionVisible('navigate_up');
});

it('formats file sizes for humans', function () {
    $page = Livewire::test(FileBrowserPage::class)->instance();

    expect($page->formatFileSize(0))->toBe('0 B')
        ->and($page->formatFileSize(5))->toBe('5 B')
        ->and($page->formatFileSize(1536))->toBe('1.5 KB')
        ->and($page->formatFileSize(3 * 1024 * 1024))->toBe('3 MB');
});
