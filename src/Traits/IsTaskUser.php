<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/* @phpstan-ignore-next-line */
trait IsTaskUser
{
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, config('ffhs-tasks.tables.task_user'), 'user_id', 'task_id');
    }

    public function displayCreatorName(): string
    {
        return $this->email;
    }
}
