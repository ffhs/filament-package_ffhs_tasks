<?php

namespace Ffhs\FfhsTasks\TaskType;

use Closure;
use Ffhs\FfhsTasks\Enums\TaskPrivacy;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Traits\HasMailTexts;
use Ffhs\FfhsTasks\Traits\HasTaskLifeCycle;
use Ffhs\FfhsUtils\Contracts\Type;
use Ffhs\FfhsUtils\Traits\IsType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

abstract class TaskType implements Type
{
    use HasMailTexts;
    use HasTaskLifeCycle;
    use IsType;

    public static function getTypeListConfig(): array
    {
        return config('ffhs-tasks.types');
    }

    public function canBeCreatedViaUi(): bool
    {
        return true;
    }

    public function canBeCancelled(): bool
    {
        return false;
    }

    public function hasStartDate(): bool
    {
        return false;
    }

    public function hasDeadline(): bool
    {
        return false;
    }

    public function shouldExpireAfterDeadline(): bool
    {
        return false;
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

        $validator->validate();

        return Task::create($data);
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
