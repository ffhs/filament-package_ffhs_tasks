<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Ffhs\FfhsTasks\Contracts\AssignableInterface;
use Ffhs\FfhsTasks\Traits\IsTaskUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements AssignableInterface
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;
    use IsTaskUser;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static string $factory = UserFactory::class;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /** Assignable Methods */
    public static function label(): string
    {
        return 'User';
    }

    public function displayName(): string
    {
        return $this->name;
    }

    public static function searchQuery(?string $search = null): Builder
    {
        return static::query()->whereLike('name', "%{$search}%");
    }

    public static function queryForUser($user): Builder
    {
        return static::query();
    }

    public function usersQuery(): Builder|Relation
    {
        return static::query();
    }
}
