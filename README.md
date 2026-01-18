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

## Usage

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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
