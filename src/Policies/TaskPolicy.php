<?php

namespace Ffhs\FfhsTasks\Policies;

use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
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

                $expectation
                    ->atLeast()
                    ->once()
                    ->andReturn($result);
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
        // Nutzer, welche einem Task zugewiesen sind, der Ersteller eines Tasks oder Nutzer,
        // die Mitglied einer Gruppe sind, welcher der Task zugeteilt wurde, können den Task bearbeiten.
        // Zusätzlich können Nutzer mit einer speziellen Permission alle Tasks systemweit einsehen und bearbeiten,
        // unabhängig von ihrer Gruppenzugehörigkeit.
        return $task->creator?->getKey() === $user->getKey();
    }

    public function handle(User $user, Task $task): bool
    {
        return true;
    }
}
