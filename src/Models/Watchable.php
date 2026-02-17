<?php

namespace Ffhs\FfhsTasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

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
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function assignale(): MorphTo
    {
        return $this->morphTo();
    }
}
