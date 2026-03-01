<?php

namespace Database\Seeders;

use App\Models\FirstUserGroup;
use App\Models\SecondUserGroup;
use App\Models\User;
use Ffhs\FfhsTasks\Models\Assignable;
use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsTasks\Models\TaskTag;
use Ffhs\FfhsTasks\Models\Watchable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
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

        $tags = TaskTag::factory()->count(5)->create();

        $this->createBasicTasks($user, $tags);
        $this->createGroupTasks();
        $this->createTasksForNotifications($user);
    }

    private function createBasicTasks(User $user, Collection $tags): void
    {
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
            ->create([
                'starts_at' => null,
                'deadline_at' => null,
            ]);

        foreach ($tasks as $task) {
            $task->users()->attach($user->id);
            $task->tags()->attach($tags->pluck('id'));
        }

        // Canceled Tasks
        $tasks = Task::factory()
            ->count(5)
            ->cancelled()
            ->create();

        foreach ($tasks as $task) {
            $task->users()->attach($user->id);
            $task->tags()->attach($tags->pluck('id'));
        }

        // Finished tasks
        $tasks = Task::factory()
            ->count(5)
            ->completed()
            ->create();

        foreach ($tasks as $task) {
            $task->users()->attach($user->id);
            $task->tags()->attach($tags->pluck('id'));
        }
    }

    private function createGroupTasks(): void
    {
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

        // Group tasks
        $groupedTasks = Task::factory()
            ->count(10)
            ->create([
                'starts_at' => null,
                'deadline_at' => null,
            ]);

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

    private function createTasksForNotifications(User $user): void
    {
        // Deadline
        $task = Task::factory()->create([
            'type' => 'approval',
            'title' => 'Deadline Approaching',
            'starts_at' => null,
            'deadline_at' => now()->addDay(),
        ]);

        $task->users()->attach($user->id);

        // Deadline exceeded
        $task = Task::factory()->create([
            'type' => 'approval',
            'title' => 'Deadline Exceeded',
            'starts_at' => null,
            'deadline_at' => now()->subDays(7),
        ]);

        $task->users()->attach($user->id);

        // Started
        $task = Task::factory()->create([
            'type' => 'approval',
            'title' => 'Start date reached today',
            'starts_at' => now(),
            'deadline_at' => now()->addDays(14),
        ]);

        $task->users()->attach($user->id);
    }
}
