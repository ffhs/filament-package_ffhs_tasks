<?php

namespace Database\Seeders;

use App\Models\FirstUserGroup;
use App\Models\SecondUserGroup;
use App\Models\User;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\Watchable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::factory()
            ->create([
                'name' => 'Test User',
                'email' => 'dev@ffhs.ch',
                'password' => Hash::make('password'),
            ]);

        $users = User::factory()
            ->count(10)
            ->create();

        $firstUserGroups = FirstUserGroup::factory()
            ->hasAttached($users->slice(5))
            ->count(5)
            ->create();

        $secondUserGroups = SecondUserGroup::factory()
            ->hasAttached($users->slice(5))
            ->count(5)
            ->create();

        // Created tasks
        Task::factory()
            ->for($user, 'creator')
            ->count(5)
            ->create();

        // Future tasks
        Task::factory()
            ->for($user, 'creator')
            ->count(5)
            ->create([
                'starts_at' => now()->addDays(3),
            ]);

        // My Tasks
        $tasks = Task::factory()
            ->count(5)
            ->create();

        foreach ($tasks as $task) {
            $task->users()->attach($user->id);
        }

        // Canceled Tasks
        $tasks = Task::factory()
            ->count(5)
            ->cancelled()
            ->create();

        foreach ($tasks as $task) {
            $task->users()->attach($user->id);
        }

        // Finished tasks
        $tasks = Task::factory()
            ->count(5)
            ->completed()
            ->create();

        foreach ($tasks as $task) {
            $task->users()->attach($user->id);
        }

        // Group tasks
        $groupedTasks = Task::factory()
            ->count(10)
            ->create();

        $groups = collect([...$firstUserGroups, ...$secondUserGroups]);

        foreach ($groupedTasks as $task) {
            $group = $groups->random();
            $creator = $users->random();
            $randomUsers = $users->random(rand(0, 3));

            $task->creator()->associate($creator);
            $task->save();

            $userData = $randomUsers->map(fn (User $user) => [
                'task_id' => $task->id,
                'assignable_type' => User::class,
                'assignable_id' => $user->id,
            ])->toArray();

            Assignable::insert(
                $userData,
            );

            Assignable::insert([
                'task_id' => $task->id,
                'assignable_id' => $group->getKey(),
                'assignable_type' => $group::class,
            ]);

            Watchable::insert([
                'task_id' => $task->id,
                'assignable_id' => $group->getKey(),
                'assignable_type' => $group::class,
            ]);
        }
    }
}
