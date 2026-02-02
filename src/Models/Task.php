<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Database\Factories\TaskFactory;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\StatusChangedEvent;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Ffhs\FfhsUtils\Traits\HasType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;
    use HasType {
        HasType::getType as protected getTaskType;
    }
    use SoftDeletes;

    protected static string $factory = TaskFactory::class;

    protected static string $parentTypeClass = TaskType::class;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return array{
     *     status: 'Ffhs\\FfhsTasks\\Enums\\TaskStatus',
     *     can_be_cancelled: 'boolean',
     *     completed_at: 'datetime',
     *     cancelled_at: 'datetime',
     *     starts_at: 'datetime',
     *     deadline_at: 'datetime',
     *     extra: 'array',
     *     data: 'array'
     * }
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'can_be_cancelled' => 'boolean',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',

            'starts_at' => 'datetime',
            'deadline_at' => 'datetime',

            'extra' => 'array',
            'data' => 'array',
        ];
    }


    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            $task->status ??= TaskStatus::InProgress;

            if (! $task->creator_id) {
                $task->creator()->associate(auth()->user());
            }
        });

        static::updated(function (Task $task) {
            if ($task->wasChanged('status')) {
                event(new StatusChangedEvent($task));
            }
        });
    }

    public function getTable(): string
    {
        return config('ffhs-tasks.tables.tasks');
    }

    public function getType(): ?TaskType
    {
        /**@phpstan-ignore-next-line */
        return $this->getTaskType();
    }

    /** Relations */

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            config('ffhs-tasks.tables.task_user'),
            'task_id',
            'user_id'
        );
    }

    public function taskUserGroups(): HasMany
    {
        return $this->hasMany(TaskUserGroup::class, 'task_id');
    }

    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    /** Methods */

    public function isArchived(): bool
    {
        return $this->status !== TaskStatus::InProgress;
    }

    public function cancel(): bool
    {
        return $this->update([
            'status' => TaskStatus::Cancelled,
            'cancelled_at' => now(),
        ]);
    }

    public function expire(): bool
    {
        return $this->update([
            'status' => TaskStatus::Expired,
        ]);
    }

    public function complete(): bool
    {
        return $this->update([
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}
