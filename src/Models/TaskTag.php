<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Database\Factories\TaskTagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use function Ffhs\FfhsTasks\resolve_model_class;

/**
 * @property int $id
 * @property string|null $display_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FfhsTasks\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Ffhs\FfhsTasks\Database\Factories\TaskTagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag withoutTrashed()
 * @mixin \Eloquent
 */
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
