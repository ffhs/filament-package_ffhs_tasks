<?php

namespace Ffhs\FfhsTasks\TaskType;

use Closure;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Pages\CreateTask;
use Ffhs\FfhsTasks\Filament\Resources\Tasks\Schemas\TaskForm;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Traits\HasTaskLifeCycle;
use Ffhs\FfhsUtils\Contracts\Type;
use Ffhs\FfhsUtils\Traits\IsType;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class TaskType implements Type
{
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
    public function validateTaskData(array $data): array
    {
        $component = new CreateTask();
        $component->type = static::identifier();

        $schema = new Schema($component);

        TaskForm::configure($schema, $component);

        $rules = [
            ...$schema->getValidationRules(),
            'type' => ['required'],
        ];

        // Closure validation will not fail if key is not present so we
        // initialize all fields with null
        $keys = array_keys($rules);

        foreach ($keys as $key) {
            if (! Arr::has($data, $key)) {
                Arr::set($data, $key, null);
            }
        }

        $validator = Validator::make(
            $data,
            rules: $rules,
            messages: $schema->getValidationMessages(),
            attributes: $schema->getValidationAttributes(),
        );

        return $validator->validate();
    }

    /**
     * @throws ValidationException
     */
    public function createTask(array $data): Task
    {
        $validated = $this->validateTaskData($data);

        return Task::create($validated);
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
