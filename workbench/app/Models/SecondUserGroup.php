<?php

namespace App\Models;

use Database\Factories\SecondUserGroupFactory;
use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;

class SecondUserGroup extends Model implements TaskUserGroupInterface
{
    /** @use HasFactory<SecondUserGroupFactory> */
    use HasFactory;
    use Notifiable;

    protected static string $factory = SecondUserGroupFactory::class;

    public static function label(): string
    {
        return 'Second User Group';
    }

    public static function searchQuery(?string $search = null): Builder
    {
        return static::query()
            ->whereLike('display_name', "%{$search}%");
    }

    public static function queryForUser(User $user): Builder
    {
        return static::query()
            ->where('id', '>', 3);
    }

    public function displayName(): string
    {
        return $this->display_name;
    }

    public function usersQuery(): Builder|Relation
    {
        return User::query()->where('id', '>', 5);
    }
}
