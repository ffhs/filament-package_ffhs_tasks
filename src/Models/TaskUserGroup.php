<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Database\Factories\TaskUserGroupFactory;
use Ffhs\FfhsTasks\Traits\IsFfhsTaskModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property \Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface|Model|null $userGroup
 */
class TaskUserGroup extends Model
{
    /** @use HasFactory<TaskUserGroupFactory> */
    use HasFactory;
    use IsFfhsTaskModel;

    protected $fillable = [
        'task_id',
        'user_group_id',
        'user_group_type',
    ];

    protected static string $factory = TaskUserGroupFactory::class;

    protected static function configKey(): string
    {
        return 'task_user_group';
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function userGroup(): MorphTo
    {
        return $this->morphTo();
    }
}
