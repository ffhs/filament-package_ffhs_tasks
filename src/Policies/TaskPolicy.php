<?php

namespace Ffhs\FfhsTasks\Policies;

use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Support\AssignableHelper;
use Illuminate\Auth\Access\HandlesAuthorization;
use Mockery;
use Mockery\MockInterface;

use function Pest\Laravel\partialMock;

class TaskPolicy
{
    use HandlesAuthorization;

    public static function fake(array $abilities): void
    {
        partialMock(static::class, function (MockInterface $mock) use ($abilities) {
            foreach ($abilities as $ability => $result) {
                /** @var Mockery\Expectation $expectation */
                $expectation = $mock->shouldReceive($ability);

                $expectation->andReturn($result);
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
        // Special permission
        if ($user->can('updateAny', $task)) {
            return true;
        }

        // Is creator
        if ($task->creator?->getKey() === $user->getKey()) {
            return true;
        }

        // Is assigned
        if ($task->users->pluck('id')->contains($user->getKey())) {
            return true;
        }

        // Is assigned through group
        $userGroupKeys = AssignableHelper::assignablesForUser($user)->map(AssignableHelper::getCompositeKey(...));
        $taskGroupKeys = $task->assignables->map(AssignableHelper::getCompositeKey(...));

        return $userGroupKeys->intersect($taskGroupKeys)->isNotEmpty();
    }

    public function handle(User $user, Task $task): bool
    {
        return true;
    }
}
