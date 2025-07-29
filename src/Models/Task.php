<?php

namespace Ffhs\FfhsTasks\Models;

use App\Models\User;
use Ffhs\FfhsTasks\Contracts\TaskCreator;
use Ffhs\FfhsTasks\Facades\FfhsTasks;
use Ffhs\FfhsTasks\TaskType\TaskType;
use Ffhs\FfhsTasks\Traits\IsFfhsTaskModel;
use Ffhs\FfhsUtils\Traits\HasType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property ?TaskCreator $creator
 * @property Collection $users
 * @property bool $finished
 * @property bool $cancelled
 * @property int $id
 */
class Task extends Model
{
    use IsFfhsTaskModel, SoftDeletes, HasType;

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
        'cancelled'
    ];

    protected static function configKey(): string
    {
        return 'tasks';
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            FfhsTasks::config('table_names.task_user'),
            'task_id',
            'user_id'
        );
    }

    public function taskGroup(): HasMany
    {
        return $this->hasMany(TaskUserGroup::class, 'task_id');
    }

    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    public function isArchived(): bool
    {
        return $this->finished || $this->cancelled || !is_null($this->deleted_at);
    }

    protected function casts(): array
    {
        return [
            'deadline_at' => 'datetime',
            'start_at' => 'datetime',
            'settings' => 'array'
        ];
    }
}
