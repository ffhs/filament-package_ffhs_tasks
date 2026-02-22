<?php

namespace Ffhs\FfhsTasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'task_id',
        'notification_type',
        'notification_key',
    ];

    public function getTable(): string
    {
        return config('ffhs-tasks.tables.task_notification_log', 'ffhs_task_notification_log');
    }

    /**
     * @return BelongsTo<Task, $this>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
