<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Database\Factories\TaskTagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use function Ffhs\FfhsTasks\resolve_model_class;

class TaskTag extends Model
{
    /** @use HasFactory<TaskTagFactory> */
    use HasFactory;
    use SoftDeletes;

    protected static string $factory = TaskTagFactory::class;

    protected $guarded = ['id'];

    public function getTable(): string
    {
        return config('ffhs-tasks.tables.task_tags', 'ffhs_task_tags');
    }

    /**
     * @return BelongsToMany<Task, $this>
     */
    public function tasks(): BelongsToMany
    {
        $modelClass = resolve_model_class(Task::class);

        return $this->belongsToMany(
            $modelClass,
            table: config('ffhs-tasks.tables.task_tag'),
            foreignPivotKey: 'tag_id',
            relatedPivotKey: 'task_id',
        );
    }
}
