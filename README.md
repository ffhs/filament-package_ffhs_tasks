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

A `TaskCreateDataException` is thrown when required fields are missing or invalid.

## Task Types

Task types define the behavior, form fields, and lifecycle of tasks. Create a custom type by extending `TaskType`:

```php
use Ffhs\FfhsTasks\TaskType\TaskType;

class ApprovalTaskType extends TaskType
{
    protected static string $identifier = 'approval';
    protected static bool $canBeCreatedViaUi = true;
    protected static bool $canBeCancelled = true;
    protected static bool $hasStartDate = true;
    protected static bool $hasDeadline = true;
    protected static bool $canExpireAfterDeadline = true;
}
```

Register it in your config:

```php
'types' => [
    ApprovalTaskType::class,
],
```

### Static Properties

| Property | Default | Description                                                                     |
|---|---|---------------------------------------------------------------------------------|
| `$identifier` | *(required)* | Unique string identifying this type                                             |
| `$canBeCreatedViaUi` | `true` | Whether the type appears in the Filament create form                            |
| `$canBeCancelled` | `false` | Whether tasks of this type can be cancelled                                     |
| `$canExpireAfterDeadline` | `false` | Whether tasks of this type can expire (see [Task Expiration](#task-expiration)) |
| `$hasStartDate` | `false` | Enables the `starts_at` date picker                                             |
| `$hasDeadline` | `false` | Enables the `deadline_at` date picker                                           |


### Custom Form Fields

Override these methods to add type-specific Filament form components:

```php
class ApprovalTaskType extends TaskType
{
    // Fields in the main content area (below description; stored in the `extra` column)
    public function getMainComponents(): array|Closure
    {
        return [
            TextInput::make('approval_notes')->required(),
        ];
    }

    // Fields in the sidebar (below dates, privacy, tags; stored in the `extra` column)
    public function getSidebarComponents(): array|Closure
    {
        return [
            Select::make('priority')->options(['low' => 'Low', 'high' => 'High']),
        ];
    }

    // Fields shown on the "Handle Task" page (stored in the `data` column)
    public function getHandleComponents(): array|Closure
    {
        return [
            Textarea::make('resolution')->label('Resolution'),
        ];
    }
}
```

### Authorization

Override these methods to customize per-type authorization:

```php
public function canViewTask(Task $task): bool;
public function canEditTask(Task $task): bool;
public function canHandleTask(Task $task): bool;
```

## Lifecycle Hooks

Override lifecycle hooks in your `TaskType` to run custom logic during state transitions:

```php
class ApprovalTaskType extends TaskType
{
    // Mutate data before saving/completing/cancelling/expiring
    public function mutateDataBeforeSave(Task $record, array $data): array
    {
        $data['extra']['reviewed_by'] = auth()->id();
        return $data;
    }

    public function mutateDataBeforeComplete(Task $record, array $data): array { return $data; }
    public function mutateDataBeforeCancel(Task $record, array $data): array { return $data; }
    public function mutateDataBeforeExpire(Task $record, array $data): array { return $data; }

    // Run logic after a transition completes
    public function afterSave(Task $record): void { }
    public function afterComplete(Task $record): void { }
    public function afterCancel(Task $record): void { }
    public function afterExpire(Task $record): void { }
}
```

## Assignable Models

By default only `User` can be assigned to tasks. To assign tasks to groups (teams, departments, etc.), implement `AssignableInterface`:

```php
use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User;

class Department extends Model implements AssignableInterface
{
    public static function label(): string
    {
        return 'Department';
    }

    public static function searchQuery(?string $search = null): Builder
    {
        return static::query()->when($search, fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
    }

    public static function queryForUser(User $user): Builder
    {
        return static::query()->whereHas('members', fn (Builder $query) => $query->where('user_id', $user->id));
    }

    public function displayName(): string
    {
        return $this->name;
    }

    public function usersQuery(): Builder|Relation
    {
        return $this->members(); // Must return User models
    }
}
```

Register it in your config:

```php
'assignable_models' => [
    User::class,
    Department::class,
],
```

## Watchables

Tasks support **watchers** (collaborators) in addition to assignees. Watchers receive notifications but do not have edit permissions. Watchers can be added via the task form sidebar.

## Tags

The package includes a built-in tagging system. Tags can be managed through the Filament panel (`TaskTagResource`) and assigned to tasks via the form sidebar. Tags support soft deletes.

## Privacy

Tasks have two privacy levels:

- **Public** — visible to all users
- **Private** — visible only to the creator, assignees, and watchers

Set via the `privacy` field when creating a task (defaults to `public`).

## Task Expiration

When a task type sets `$canExpireAfterDeadline = true`, the create form shows a toggle for `expires_after_deadline`. If enabled on a task, the `ExpireOverdueTasksJob` (runs every minute) automatically transitions overdue tasks to the `Expired` status.

## Model Overrides

Swap any internal model with your own subclass via the `models` config key:

```php
'models' => [
    \Ffhs\FfhsTasks\Models\Task::class => \App\Models\CustomTask::class,
],
```

This works for `Task`, `NotificationLog`, `Assignable`, and `Watchable`.

## Events

The package dispatches events for key moments in a task's lifecycle:

| Event | When |
|---|---|
| `StatusChangedEvent` | Task status changes (e.g. InProgress to Completed) |
| `TaskStartedEvent` | Task reaches its `starts_at` date |
| `TaskReachedDeadlineEvent` | Task reaches its `deadline_at` date |
| `TaskExpiredEvent` | Task expires after its deadline |

`TaskStartedEvent` and `TaskReachedDeadlineEvent` are dispatched once per task via scheduled jobs that run every minute. `TaskExpiredEvent` is dispatched when a task actually expires via the `expire()` method.

```php
use Ffhs\FfhsTasks\Events\TaskStartedEvent;

Event::listen(TaskStartedEvent::class, function (TaskStartedEvent $event) {
    $event->task; // The task that started
});
```

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
        Notifications\WeeklyTasksNotification::class,
    ],
    'deadline_remind_before' => [CarbonInterval::days(7), CarbonInterval::days(3), CarbonInterval::days(1)],
    'deadline_remind_after' => [CarbonInterval::hours(0), CarbonInterval::days(3), CarbonInterval::days(7)],
    'weekly_tasks' => [
        'time' => '08:00', // Sent on Mondays at this time
    ],
],
```

Only notifications listed in the `enabled` array will be sent. An empty array disables all notifications.

`WeeklyTasksNotification` sends a digest of all tasks with deadlines in the current week. It is sent every Monday at the configured time.

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
