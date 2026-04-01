<?php

namespace Ffhs\FfhsTasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

use function Ffhs\FfhsTasks\resolve_model_class;

/**
 * @property int $id
 * @property int $task_id
 * @property string $assignable_type
 * @property int $assignable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model $assignable
 * @property-read \Ffhs\FfhsTasks\Models\Task|null $task
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Watchable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Watchable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Watchable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Watchable whereAssignableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Watchable whereAssignableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Watchable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Watchable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Watchable whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Watchable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Watchable extends Pivot
{
    protected $fillable = [
        'task_id',
        'assignable_id',
        'assignable_type',
    ];

    public function getTable(): string
    {
        return config('ffhs-tasks.tables.task_watchables');
    }

    /**
     * @return BelongsTo<Task, $this>
     */
    public function task(): BelongsTo
    {
        $modelClass = resolve_model_class(Task::class);

        return $this->belongsTo($modelClass, 'task_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }
}
