<?php

namespace Ffhs\FfhsTasks\Jobs;

use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class ExpireOverdueTasksJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Task::query()
            ->where('status', TaskStatus::InProgress)
            ->where('deadline_at', '<', Carbon::now())
            ->chunkById(100, function ($tasks) {
                foreach ($tasks as $task) {
                    $task->update(['status' => TaskStatus::Expired]);
                }
            });
    }
}
