<?php

namespace Ffhs\FfhsTasks\Models;

use Carbon\Carbon;
use Ffhs\FfhsTasks\Contracts\TaskCreator;
use Ffhs\FfhsTasks\Database\Factories\TaskFactory;
use Ffhs\FfhsTasks\Enums\TaskStatus;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Ffhs\FfhsUtils\Traits\HasType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;

/**
 * @property ?TaskCreator $creator
 * @property Collection $users
 * @property bool $finished
 * @property bool $cancelled
 * @property ?Carbon $deadline_at
 * @property int $id
 * @property string $title
 * @property bool $can_cancel
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

    protected $fillable = [
        'title',
        'description',
        'type',
        'settings',
        'data',
        'finished',
        'deadline_at',
        'start_at',
        'creator_type',
        'creator_id',
        'can_cancel',
        'cancelled',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'can_be_cancelled' => 'boolean',
            'finished_at' => 'datetime',
            'cancelled_at' => 'datetime',

            'starts_at' => 'datetime',
            'deadline_at' => 'datetime',

            'data' => 'array',
            'settings' => 'array',
        ];
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

    public function isArchived(): bool
    {
        if ($this->finished || $this->cancelled) {
            return true;
        }

        return ! is_null($this->deadline_at);
    }

    protected function casts(): array
    {
        return [
            'deadline_at' => 'datetime',
            'start_at' => 'datetime',
            'settings' => 'array',
        ];
    }
}
