<?php

namespace Ffhs\FfhsTasks\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Auth\User;

interface TaskUserGroupInterface
{
    public static function getGroupsQuery(?string $search = null): Builder;

    public static function getGroupsForUserQuery(User $user): Builder;

    public static function groupDisplayname(): string;

    public function getGroupModelTitle(): string;

    public function groupUsersQuery(): Builder|Relation;
}
