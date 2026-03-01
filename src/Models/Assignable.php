<?php

namespace Ffhs\FfhsTasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

use function Ffhs\FfhsTasks\resolve_model_class;

class Assignable extends Pivot
{
    protected $fillable = [
        'task_id',
        'assignable_id',
        'assignable_type',
    ];

    public function getTable(): string
    {
        return config('ffhs-tasks.tables.task_assignables');
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
