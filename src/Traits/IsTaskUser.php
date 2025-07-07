<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Models\Task;

trait IsTaskUser
{
    public function tasks()
    {
        return $this->belongsToMany(Task::class, FfhsTasks::config('table_names.task_user'), 'user_id', 'task_id');
    }

    public function displayCreatorName(): string
    {
        return $this->email;
    }
}
