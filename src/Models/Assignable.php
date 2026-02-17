<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Database\Factories\AssignableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Assignable extends Pivot
{
    /** @use HasFactory<AssignableFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'assignable_id',
        'assignable_type',
    ];

    protected static string $factory = AssignableFactory::class;

    public function getTable(): string
    {
        return config('ffhs-tasks.tables.task_assignables', 'ffhs_task_assignables');
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
    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }
}
