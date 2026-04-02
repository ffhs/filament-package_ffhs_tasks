<?php

namespace Ffhs\FfhsTasks\Models;

use App\Models\User;
use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

class TaskGroup extends Model implements AssignableInterface
{
    protected $fillable = [
        'title',
        'bulk_address',
    ];

    public static function label(): string
    {
        return 'Task Group'; //ToDo Translate
    }

    public static function searchQuery(?string $search = null): Builder
    {
        return static::query()->whereLike('title', "%{$search}%");
    }

    public static function queryForUser(\Illuminate\Foundation\Auth\User $user): Builder
    {
        return static::query()->whereHas('users', fn (Builder $query) => $query->where('users.id', $user->getKey()));
    }

    public function getTable(): string
    {
        return config('ffhs-tasks.tables.task_groups');
    }

    public function displayName(): string
    {
        return $this->title;
    }

    public function usersQuery(): Builder|Relation
    {
        return $this->users();
    }

    /**
     * @return BelongsToMany<User, self>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('ffhs-tasks.user.model'),
            config('ffhs-tasks.tables.task_group_user'),
            'task_group_id',
            'user_id'
        );
    }
}
