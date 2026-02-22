# FFHS Tasks

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ffhs/filament-package_ffhs_tasks.svg?style=flat-square)](https://packagist.org/packages/ffhs/filament-package_ffhs_tasks)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ffhs/filament-package_ffhs_tasks/test.yml?branch=main&label=tests&style=flat-square)](https://github.com/ffhs/filament-package_ffhs_tasks/actions?query=workflow%3Atest+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ffhs/filament-package_ffhs_tasks/format.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ffhs/filament-package_ffhs_tasks/actions?query=workflow%3Aformat+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ffhs/filament-package_ffhs_tasks.svg?style=flat-square)](https://packagist.org/packages/ffhs/filament-package_ffhs_tasks)


FFHS Tasks is a task management plugin for FilamentPHP.


## Development

The package is using `orchestral/testbench` for testing against a Laravel app.

`composer serve` will start the application as `http://127.0.0.1:8000`.


## Installation

You can install the package via composer:

```bash
composer require ffhs/filament-package_ffhs_tasks
```

Then publish and run the migrations with:

```bash
php artisan vendor:publish --tag="ffhs-tasks-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="ffhs-tasks-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="ffhs-tasks-views"
```

## Setup

Add the plugin to your Filament panel:

```php
// In your Panel Service Provider
use Ffhs\FfhsTasks\FfhsTasksPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->plugin(
            FfhsTasksPlugin::make()                
        );
}
```

Implement the `IsTaskUser` trait on your `User` model:

```php
class User extends Authenticatable
{
    use IsTaskUser;
}
```



## Programmatic Task Creation

Tasks can be created programmatically via the `createTask()` method on any `TaskType` instance. This method validates the input data against the same form rules used in the Filament UI (including type-specific fields from `getMainComponents()` and `getSidebarComponents()`) and then creates the task.

```php
use Ffhs\FfhsTasks\TaskType\TaskType;

$taskType = TaskType::getTypeFromIdentifier('approval');

$task = $taskType->createTask([
    'type' => 'approval',
    'title' => 'Review budget proposal',
    'description' => 'Please review the Q3 budget proposal.',
    'privacy' => 'public',
    'can_be_cancelled' => true,
    'starts_at' => '2026-03-01 10:00:00',   // optional, if the type supports it
    'deadline_at' => '2026-03-15 18:00:00',  // optional, if the type supports it
    'extra' => [
        // Type-specific fields defined in getMainComponents() / getSidebarComponents()
        'approval_notes' => 'Needs CFO sign-off',
    ],
]);
```

A `ValidationException` is thrown when required fields are missing or invalid.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
