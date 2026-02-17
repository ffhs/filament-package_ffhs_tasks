<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Database\Factories\TaskFactory;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\StatusChangedEvent;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Ffhs\FfhsUtils\Traits\HasType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        return config('ffhs-tasks.tables.tasks', 'ffhs_tasks');
    }

    public function getType(): ?TaskType
    {
        /**@phpstan-ignore-next-line */
        return $this->getTaskType();
    }

    /** Relations */

    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            config('ffhs-tasks.user.model'),
            'assignable',
            config('ffhs-tasks.tables.task_assignables'),
        );
    }

    /**
     * @return HasMany<Assignable, $this>
     */
    public function assignables(): HasMany
    {
        return $this->hasMany(Assignable::class, 'task_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    /** Methods */

    public function canBeEdited(): bool
    {
        return ! $this->isArchived();
    }

    public function isArchived(): bool
    {
        return $this->status !== TaskStatus::InProgress;
    }

    public function cancel(array $data = []): void
    {
        $taskType = $this->getType();

        $data = [
            ...$data,
            'status' => TaskStatus::Cancelled,
            'cancelled_at' => now(),
        ];

        if ($taskType) {
            $data = $taskType->mutateDataBeforeCancel($this, $data);
        }

        $this->update($data);

        $taskType?->afterCancel($this);
    }

    public function expire(array $data = []): void
    {
        $taskType = $this->getType();

        $data = [
            ...$data,
            'status' => TaskStatus::Expired,
        ];

        if ($taskType) {
            $data = $taskType->mutateDataBeforeExpire($this, $data);
        }

        $this->update($data);

        $taskType?->afterExpire($this);
    }

    public function complete(array $data = []): void
    {
        $taskType = $this->getType();

        $data = [
            ...$data,
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ];

        if ($taskType) {
            $data = $taskType->mutateDataBeforeComplete($this, $data);
        }

        $this->update($data);

        $taskType?->afterComplete($this);
    }
}
