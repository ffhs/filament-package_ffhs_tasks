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
use Carbon\Carbon;
use Ffhs\FfhsTasks\TaskType\TaskType;

$taskType = TaskType::getTypeFromIdentifier('approval');

$task = $taskType->createTask([
    'title' => 'Review budget proposal',
    'description' => 'Please review the Q3 budget proposal.',
    'privacy' => 'public',
    'can_be_cancelled' => true,
    'starts_at' => \Illuminate\Support\Facades\Date::parse('2026-03-01 10:00:00'),   // optional, if the type supports it
    'deadline_at' => \Illuminate\Support\Facades\Date::parse('2026-03-15 18:00:00'),  // optional, if the type supports it
    'extra' => [
        // Type-specific fields defined in getMainComponents() / getSidebarComponents()
        'approval_notes' => 'Needs CFO sign-off',
    ],
]);
```

A `ValidationException` is thrown when required fields are missing or invalid.

## Notifications

The package ships with notifications for task lifecycle events. Enable them individually by adding their class names to the `enabled` array in your config:

```php
// config/ffhs-tasks.php
use Ffhs\FfhsTasks\Notifications;

'notifications' => [
    'enabled' => [
        Notifications\TaskAssignedNotification::class,
        Notifications\TaskStatusChangedNotification::class,
        Notifications\TaskStartDateReachedNotification::class,
        Notifications\TaskDeadlineApproachingNotification::class,
        Notifications\TaskDeadlineExceededNotification::class,
    ],
    'deadline_remind_before' => [CarbonInterval::days(7), CarbonInterval::days(3), CarbonInterval::days(1)],
    'deadline_remind_after' => [CarbonInterval::hours(0), CarbonInterval::days(3), CarbonInterval::days(7)],
],
```

Only notifications listed in the `enabled` array will be sent. An empty array disables all notifications.

Intervals use `CarbonInterval`, so you can mix units like `CarbonInterval::hours(12)` or `CarbonInterval::days(3)`. The smallest supported unit is 1 hour.

### Customizing Notification Intervals Per Task Type

Override `deadlineRemindBefore()` or `deadlineRemindAfter()` in your `TaskType` to use different intervals per type:

```php
class ApprovalTaskType extends TaskType
{
    public function deadlineRemindBefore(): array
    {
        return [CarbonInterval::days(14), CarbonInterval::days(7), CarbonInterval::hours(12)];
    }

    public function deadlineRemindAfter(): array
    {
        return [CarbonInterval::hours(0), CarbonInterval::days(1)];
    }
}
```

### Mail Recipients for Group Assignables

When a notification is sent to a group assignable (i.e. a model implementing `AssignableInterface` that is not a `User`), the package determines the recipient as follows:

1. If the model uses the `Notifiable` trait **and** defines a `routeNotificationForMail()` method that returns a non-empty address, the notification is sent directly to that address.
2. Otherwise, the notification is sent individually to every user returned by the model's `usersQuery()` method.

To send group notifications to a single address instead of every member, add `routeNotificationForMail()` to your assignable model:

```php
use Illuminate\Notifications\Notifiable;

class Department extends Model implements AssignableInterface
{
    use Notifiable;

    public function routeNotificationForMail(Notification $notification): string
    {
        return $this->email; // e.g. "department@example.com"
    }
}
```

### Customizing Mail Content

Override `getMailForNotification()` in your `TaskType` to fully customize the mail for any notification. Return a `MailMessage` to replace the default, or `null` to use the translation-based default:

```php
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Notifications\TaskDeadlineApproachingNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalTaskType extends TaskType
{
    public function getMailForNotification(Notification $notification, Task $task): ?MailMessage
    {
        if ($notification instanceof TaskDeadlineApproachingNotification) {
            return (new MailMessage())
                ->subject("Approval needed: {$task->title}")
                ->greeting('Action required')
                ->line("The approval for \"{$task->title}\" is due in {$notification->remainingTime->forHumans()}.");
        }

        return null;
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
