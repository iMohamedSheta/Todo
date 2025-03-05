# Todo Package

# Laravel TODO Attribute Scanner

A small Laravel package that helps you track and manage technical debt by scanning your codebase for `#[TODO]` attributes.

## Features ✨

- **Multi-level Scanning** - Find TODOs in classes, methods, and functions
- **Customizable Sources** - Scan specific directories or files
- **Clear Reporting** - Beautiful console table output with counts
- **Custom Messages** - Add detailed TODO descriptions
- **Laravel Integration** - Native Artisan command integration
- **Modern PHP With Laravel** - Built for Laravel with PHP 8.1+ with attributes

## Installation 📦

1. Install via Composer:

```bash
composer require laravel-attributes/todo
```

2. The package will auto-register its service provider and command.

## Usage 🚀

## Adding TODOs 📝

### Class-level TODO
```php
use IMohamedSheta\Todo\Attributes\TODO;
use IMohamedSheta\Todo\Enums\Priority;

#[TODO('Need to create extractChunks.', Priority::Medium)]
class ExcelTextExtractor
{
    // Class implementation...
}
```

### Basic Scan
Scan default directory (app):
```bash
php artisan todo
```

### Sample Output
```bash
🔍 Scanning for TODOs...

+----------+------------------------------------------------------+----------+----------------------------------+
| Type     | Class/Method/Function                                | Priority | Message                          |
+----------+------------------------------------------------------+----------+----------------------------------+
| Class    | App\Extractors\FileTextExtractors\ExcelTextExtractor | Medium   | Need to create extractChunks.    |
| Method   | App\Actions\Auth\RegisterAction::createClinicAdmin() | High     | Generate a unique billing code   |
| Function | app\Helpers\helpers.php -> array_only()              | Medium   | Not finished yet                 |
+----------+------------------------------------------------------+----------+----------------------------------+

🎯 Total TODOs Found: 3
```

### Method-level TODO
```php
use IMohamedSheta\Todo\Attributes\TODO;
use IMohamedSheta\Todo\Enums\Priority;

class RegisterAction
{
    #[TODO('Generate a unique billing code for the clinic', Priority::High)]
    public function createClinicAdmin()
    {
        // Method implementation...
    }
}
```

### Function-level TODO
```php
use IMohamedSheta\Todo\Attributes\TODO;
use IMohamedSheta\Todo\Enums\Priority;

#[TODO('Not finished yet', Priority::Medium)]
function array_only()
{
    // Function logic...
}
```

## Configuration ⚙️

### Default Message
When no message is provided:
```php
#[TODO] // Shows "Not finished yet" in output and priority medium as default
class PendingFeature
{
    // ...
}
```

### Custom Source Directories
Scan specific directories:
```bash
php artisan todo --src=app/Http/Controllers
```

## License 📄

This package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
