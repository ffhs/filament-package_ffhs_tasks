<?php

namespace Ffhs\FfhsTasks\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User;

interface AssignableInterface
{
    /**
     * The label for this class
     */
    public static function label(): string;

    public static function searchQuery(?string $search = null): Builder;

    public static function queryForUser(User $user): Builder;

    public function displayName(): string;

    public function usersQuery(): Builder|Relation;
}
