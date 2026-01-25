<?php

namespace Ffhs\FfhsTasks\Events;

use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;

class StatusChangedEvent
{
    use Dispatchable;

    public function __construct(
        public Task $task
    ) {
    }
}
