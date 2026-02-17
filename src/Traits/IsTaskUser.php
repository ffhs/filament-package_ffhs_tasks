<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/* @phpstan-ignore-next-line */
trait IsTaskUser
{
    public function tasks(): BelongsToMany
    {
        return $this->morphToMany(
            Task::class,
            'assignable',
            config('ffhs-tasks.tables.task_assignables'),
        );
    }

    public function displayCreatorName(): string
    {
        return $this->email;
    }
}
