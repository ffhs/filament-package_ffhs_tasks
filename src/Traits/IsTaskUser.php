<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use function Ffhs\FfhsTasks\resolve_model_class;

/* @phpstan-ignore-next-line */
trait IsTaskUser
{
    public function tasks(): BelongsToMany
    {
        $modelClass = resolve_model_class(Task::class);

        return $this->morphToMany(
            $modelClass,
            'assignable',
            config('ffhs-tasks.tables.task_assignables'),
            relatedPivotKey: 'task_id',
        );
    }

    public function displayCreatorName(): string
    {
        return $this->email;
    }
}
