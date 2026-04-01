<?php

namespace Ffhs\FfhsTasks\Models;

use Ffhs\FfhsTasks\Database\Factories\TaskFactory;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\Events\StatusChangedEvent;
use Ffhs\FfhsTasks\Events\TaskExpiredEvent;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Ffhs\FfhsUtils\Traits\HasType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use function Ffhs\FfhsTasks\resolve_model_class;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property TaskStatus $status
 * @property string $privacy
 * @property string|null $creator_type
 * @property int|null $creator_id
 * @property bool $can_be_cancelled
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $deadline_at
 * @property array<array-key, mixed>|null $extra
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FfhsTasks\Models\Assignable> $assignables
 * @property-read int|null $assignables_count
 * @property-read \Illuminate\Database\Eloquent\Model|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FfhsTasks\Models\NotificationLog> $notificationLogs
 * @property-read int|null $notification_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FfhsTasks\Models\TaskTag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ffhs\FfhsTasks\Models\Watchable> $watchables
 * @property-read int|null $watchables_count
 * @method static \Ffhs\FfhsTasks\Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCanBeCancelled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDeadlineAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task wherePrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withoutTrashed()
 * @mixin \Eloquent
 */
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

    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            $task->status ??= TaskStatus::InProgress;

            if (!$task->creator_id) {
                $task->creator()->associate(auth()->user());
            }
        });

        static::updated(function (Task $task) {
            if ($task->wasChanged('status')) {
                event(new StatusChangedEvent($task));
            }
        });
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    public function getTable(): string
    {
        return config('ffhs-tasks.tables.tasks', 'ffhs_tasks');
    }

    /** Relations */

    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            config('ffhs-tasks.user.model'),
            'assignable',
            config('ffhs-tasks.tables.task_assignables'),
            'task_id',
        );
    }

    /**
     * @return HasMany<Assignable, $this>
     */
    public function assignables(): HasMany
    {
        $modelClass = resolve_model_class(Assignable::class);

        return $this->hasMany($modelClass, 'task_id');
    }

    /**
     * @return HasMany<Watchable, $this>
     */
    public function watchables(): HasMany
    {
        $modelClass = resolve_model_class(Watchable::class);

        return $this->hasMany($modelClass, 'task_id');
    }

    /**
     * @return HasMany<NotificationLog, $this>
     */
    public function notificationLogs(): HasMany
    {
        $modelClass = resolve_model_class(NotificationLog::class);

        return $this->hasMany($modelClass, 'task_id');
    }

    /**
     * @return BelongsToMany<TaskTag, $this>
     */
    public function tags(): BelongsToMany
    {
        $modelClass = resolve_model_class(TaskTag::class);

        return $this->belongsToMany(
            $modelClass,
            table: config('ffhs-tasks.tables.task_tag'),
            foreignPivotKey: 'task_id',
            relatedPivotKey: 'tag_id',
        );
    }

    /** Methods */

    public function canBeEdited(): bool
    {
        return !$this->isArchived();
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

    public function getType(): ?TaskType
    {
        /**@phpstan-ignore-next-line */
        return $this->getTaskType();
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

        event(new TaskExpiredEvent($this));

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

    /**
     * @return array{
     *     status: 'Ffhs\\FfhsTasks\\Enums\\TaskStatus',
     *     can_be_cancelled: 'boolean',
     *     expires_after_deadline: 'boolean',
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
            'expires_after_deadline' => 'boolean',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',

            'starts_at' => 'datetime',
            'deadline_at' => 'datetime',

            'extra' => 'array',
            'data' => 'array',
        ];
    }
}
