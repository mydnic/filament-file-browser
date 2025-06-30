# A S3-compatible file browser to list, delete and upload files on any configured disks

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mydnic/filament-file-browser.svg?style=flat-square)](https://packagist.org/packages/mydnic/filament-file-browser)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mydnic/filament-file-browser/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mydnic/filament-file-browser/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mydnic/filament-file-browser/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mydnic/filament-file-browser/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mydnic/filament-file-browser.svg?style=flat-square)](https://packagist.org/packages/mydnic/filament-file-browser)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require mydnic/filament-file-browser
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-file-browser-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-file-browser-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-file-browser-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentFileBrowser = new Mydnic\FilamentFileBrowser();
echo $filamentFileBrowser->echoPhrase('Hello, Mydnic!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mydnic](https://github.com/mydnic)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
