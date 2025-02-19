# Todo Package (Under Construction 🚧)

⚠️ This package is still under development. It is **not finished** and **not tested** yet. Use at your own risk!

# Laravel TODO Attribute Scanner

A comprehensive Laravel package that helps you track and manage technical debt by scanning your codebase for `#[TODO]` attributes. Perfect for teams wanting to maintain clean codebases and track pending tasks directly in their source code.

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
composer require imohamedsheta/todo:dev-main
```

2. The package will auto-register its service provider and command.

## Usage 🚀

### Basic Scan
Scan default directory (app):
```bash
php artisan todo:scan
```

### Sample Output
```bash
🔍 Scanning for TODOs...

+----------+-----------------------------------+-------------------------------+
| Type     | Class/Method                      | Message                       |
+----------+-----------------------------------+-------------------------------+
| Class    | App\Models\Subscription           | Implement renewal logic       |
| Method   | App\Services\Payment::process     | Add currency conversion       |
| Function | routes/api.php -> rateLimit()     | Implement dynamic throttling  |
+----------+-----------------------------------+-------------------------------+

🎯 Total TODOs Found: 3
```

## Adding TODOs 📝

### Class-level TODO
```php
<?php

namespace App\Models;

use IMohamedSheta\Todo\Attributes\TODO;

#[TODO('Implement soft deletion support')]
class Post
{
    // Class implementation...
}
```

### Method-level TODO
```php
namespace App\Services;

class NotificationService
{
    #[TODO('Add SMS notification support')]
    public function sendReminder()
    {
        // Method implementation...
    }
}
```

### Function-level TODO
```php
<?php

use IMohamedSheta\Todo\Attributes\TODO;

#[TODO('Optimize database query')]
function generateReport() {
    // Function logic...
}
```

## Configuration ⚙️

### Default Message
When no message is provided:
```php
#[TODO] // Shows "Not finished yet" in output
class PendingFeature
{
    // ...
}
```

### Custom Source Directories
Scan specific directories:
```bash
php artisan todo:scan --src=app/Http/Controllers
```

## Testing 🧪
this package not finished

## License 📄

This package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
