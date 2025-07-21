<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskUserGroup;

trait IsUserGroup
{
    public function taskGroups()
    {
        return $this->morphMany(TaskUserGroup::class, 'user_group');
    }
}
