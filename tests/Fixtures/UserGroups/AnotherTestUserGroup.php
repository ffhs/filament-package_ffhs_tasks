<?php

namespace Ffhs\FfhsTasks\Tests\Fixtures\UserGroups;

use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User;

class AnotherTestUserGroup extends Model implements TaskUserGroupInterface
{
    use HasFactory;

    protected $table = 'second_user_groups';

    protected $guarded = [];

    public static function label(): string
    {
        return 'Another Test User Group';
    }

    public static function searchQuery(?string $search = null): Builder
    {
        return static::query()
            ->when($search, fn (Builder $query, string $search) => $query->whereLike('display_name', "%{$search}%"));
    }

    public static function queryForUser(User $user): Builder
    {
        return static::query()
            ->whereHas('users', fn (Builder $query) => $query->where('users.id', $user->getKey()));
    }

    public function displayName(): string
    {
        return $this->display_name;
    }

    public function usersQuery(): Builder|Relation
    {
        return $this->users();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'test_user_group_user', 'user_group_id', 'user_id');
    }

    protected static function newFactory(): AnotherTestUserGroupFactory
    {
        return new AnotherTestUserGroupFactory();
    }
}
