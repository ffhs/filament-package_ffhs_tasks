<?php

namespace App\Models;

use Database\Factories\FirstUserGroupFactory;
use Ffhs\FfhsTasks\Contracts\TaskUserGroupInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;

class FirstUserGroup extends Model implements TaskUserGroupInterface
{
    /** @use HasFactory<FirstUserGroupFactory> */
    use HasFactory;
    use Notifiable;

    protected static string $factory = FirstUserGroupFactory::class;

    public static function label(): string
    {
        return 'First User Group';
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
        return User::query()->where('id', '<=', 5);
    }
    //
    // public function getCollectEmailAddres(): ?null
    // {
    //     return null;
    // }
}
