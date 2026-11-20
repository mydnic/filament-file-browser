# AGENTS.md

A Filament 5 plugin that browses, uploads and deletes files on any configured Laravel
filesystem disk. It is a package, not an application: there is no `artisan`, no database
and no migrations.

## Layout

- `src/Pages/FileBrowserPage.php` - the Livewire page: navigation, selection, upload, delete.
- `src/Services/FileBrowserService.php` - all filesystem work. Everything that touches a disk
  belongs here, not in the page.
- `src/FilamentFileBrowser.php` + `src/Facades/` - thin facade over the service for host apps.
- `resources/views/pages/file-browser.blade.php` - the page view, plus the list partial it includes.
- `config/filament-file-browser.php` - published config. Read it with `config()` defaults, never
  assume the host has published it.

## Commands

```bash
composer install
composer test      # vendor/bin/pest
composer analyse   # phpstan, level 4, src + config only
composer format    # pint, laravel preset
```

CI runs the suite on PHP 8.3/8.4 x Laravel 12/13 x prefer-lowest/prefer-stable x
ubuntu/windows, so avoid anything platform specific in tests: no hardcoded directory
separators, no mime type assertions, no reliance on filesystem ordering.

## Testing

Pest, with `tests/TestCase.php` booting Orchestra Testbench plus the Filament service
providers. `tests/TestPanelProvider.php` registers a default panel so the page renders the
way it would in a host application; `Filament::setCurrentPanel()` is already done in `setUp`.

- Use `Storage::fake()` for every disk a test touches. `executionOrder` is random, so tests
  must not depend on each other.
- `temporaryUpload()` in `tests/Pest.php` builds a real `TemporaryUploadedFile` without an
  HTTP request. Use it for anything that exercises an upload.
- A test that only asserts on component state is not enough: let the page render, because a
  broken Blade or a broken action schema only shows up there.
