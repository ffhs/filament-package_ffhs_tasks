<?php

namespace Ffhs\FfhsTasks\TaskType;

use Closure;
use Ffhs\FfhsTasks\Enums\TaskPrivacy;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Exceptions\TaskCreateDataException;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Traits\HasMailTexts;
use Ffhs\FfhsTasks\Traits\HasTaskLifeCycle;
use Ffhs\FfhsUtils\Contracts\Type;
use Ffhs\FfhsUtils\Traits\IsType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use RuntimeException;

abstract class TaskType implements Type
{
    use HasMailTexts;
    use HasTaskLifeCycle;
    use IsType;


    protected static bool $canBeCreatedViaUi = true;
    protected static bool $canBeCancelled = true;
    protected static bool $hasStartDate = false;
    protected static bool $hasDeadline = false;
    protected static bool $shouldExpireAfterDeadline = false;
    protected static string $identifier;


    public static function getTypeListConfig(): array
    {
        return config('ffhs-tasks.types');
    }

    public function canBeCreatedViaUi(): bool
    {
        return static::$canBeCreatedViaUi;
    }

    public function canBeCancelled(): bool
    {
        return static::$canBeCancelled;
    }

    public function hasStartDate(): bool
    {
        return static::$hasStartDate;
    }

    public function hasDeadline(): bool
    {
        return static::$hasDeadline;
    }

    public function shouldExpireAfterDeadline(): bool
    {
        return static::$shouldExpireAfterDeadline;
    }

    public function canViewTask(Task $task): bool
    {
        return auth()->user()->can('view', $task);
    }

    public function canEditTask(Task $task): bool
    {
        return auth()->user()->can('update', $task);
    }

    public function canHandleTask(Task $task): bool
    {
        return auth()->user()->can('handle', $task) && ($task->starts_at === null || $task->starts_at->isPast());
    }

    /**
     * @throws ValidationException
     */
    public function createTask(array $data): Task
    {
        $data = [
            'type' => static::identifier(),
            'status' => TaskStatus::InProgress,
            'privacy' => TaskPrivacy::Public,
            ...$data,
        ];

        $validator = Validator::make(
            $data,
            rules: [
                'type' => ['required'],
                'title' => ['required'],
                'description' => ['required'],
                'privacy' => ['required', new Enum(TaskPrivacy::class)],
                'status' => ['required', new Enum(TaskStatus::class)],
                'data' => ['nullable', 'array'],
                'extra' => ['nullable', 'array'],

            ],
            attributes: [
                'title' => __('ffhs-tasks::tasks.attributes.title'),
                'description' => __('ffhs-tasks::tasks.attributes.description'),
                'privacy' => __('ffhs-tasks::tasks.attributes.privacy'),
                'status' => __('ffhs-tasks::tasks.attributes.status'),
            ],
        );


        try {
            $validator->validate();
        } catch (ValidationException $e) {
            throw new TaskCreateDataException('Validation failed: ' . json_encode($e->errors()), previous: $e);
        }

        return Task::create($data);
    }

    public static function identifier(): string
    {
        if (!isset(static::$identifier)) {
            throw new RuntimeException('Task type identifier must be set [' . static::class . ']');
        }
        return static::$identifier;
    }

    public function getMainComponents(): array|Closure
    {
        return [];
    }

    public function getSidebarComponents(): array|Closure
    {
        return [];
    }

    public function getHandleComponents(): array|Closure
    {
        return [];
    }
}
