<?php

namespace Ffhs\FfhsTasks\Traits;

use Ffhs\FfhsTasks\Models\TaskUserGroup;

trait IsUserGroup
{
    public function taskUserGroups()
    {
        return $this->morphMany(TaskUserGroup::class, 'user_group');
    }
}
