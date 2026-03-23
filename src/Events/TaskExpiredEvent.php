<?php

namespace Ffhs\FfhsTasks\Events;

use Ffhs\FfhsTasks\Models\Task;

final readonly class TaskExpiredEvent
{
    public function __construct(
        public Task $task
    ) {
    }
}
