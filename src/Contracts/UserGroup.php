<?php

namespace Ffhs\FfhsTasks\Contracts;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface UserGroup
{
    public function usersQuery(): Builder;
    public function userModels(): Collection;
}
