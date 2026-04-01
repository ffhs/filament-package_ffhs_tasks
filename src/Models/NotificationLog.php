<?php

namespace Ffhs\FfhsTasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use function Ffhs\FfhsTasks\resolve_model_class;

/**
 * @property int $id
 * @property int $task_id
 * @property string $notification_type
 * @property string $notification_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Ffhs\FfhsTasks\Models\Task|null $task
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationLog whereNotificationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationLog whereNotificationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationLog whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        $modelClass = resolve_model_class(Task::class);

        return $this->belongsTo($modelClass, 'task_id');
    }
}
