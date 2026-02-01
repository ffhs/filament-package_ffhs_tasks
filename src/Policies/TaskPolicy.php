<?php

namespace Ffhs\FfhsTasks\Policies;

use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;
use Mockery\MockInterface;

use function Pest\Laravel\partialMock;

class TaskPolicy
{
    use HandlesAuthorization;

    public static function fake(array $abilities): void
    {
        partialMock(static::class, function (MockInterface $mock) use ($abilities) {
            foreach ($abilities as $ability => $result) {
                $mock->shouldReceive($ability)
                    ->andReturn($result)
                    ->atLeast()
                    ->once();
            }
            $mock->shouldIgnoreMissing();
        });
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        return $task->creator?->getKey() === $user->getKey();
    }

    public function handle(User $user, Task $task): bool
    {
        return true;
    }
}
