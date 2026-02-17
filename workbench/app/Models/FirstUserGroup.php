<?php

namespace App\Models;

use Database\Factories\FirstUserGroupFactory;
use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Notifiable;

class FirstUserGroup extends Model implements AssignableInterface
{
    /** @use HasFactory<FirstUserGroupFactory> */
    use HasFactory;
    use Notifiable;

    protected static string $factory = FirstUserGroupFactory::class;

    public static function label(): string
    {
        return 'First User Group';
    }

    public function displayName(): string
    {
        return $this->display_name;
    }

    public static function searchQuery(?string $search = null): Builder
    {
        return static::query()
            ->whereLike('display_name', "%{$search}%");
    }

    public static function queryForUser($user): Builder
    {
        return static::query()
            ->whereHas('users', fn (Builder $query) => $query->where('users.id', $user->getKey()));
    }

    public function usersQuery(): Builder|Relation
    {
        return $this->users();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'test_user_group_user', 'user_group_id', 'user_id');
    }
}
