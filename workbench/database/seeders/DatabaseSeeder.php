<?php

namespace Database\Seeders;

use App\Models\FirstUserGroup;
use App\Models\SecondUserGroup;
use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            ->count(5)
            ->create();

        $secondUserGroups = SecondUserGroup::factory()
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
        Task::factory()
            ->hasAttached($user)
            ->count(5)
            ->create();

        // Canceled Tasks
        Task::factory()
            ->hasAttached($user)
            ->count(5)
            ->cancelled()
            ->create();

        // Finished tasks
        Task::factory()
            ->hasAttached($user)
            ->count(5)
            ->completed()
            ->create();

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
            $task->users()->attach($randomUsers);
            $task->save();

            DB::table('ffhs_task_user_group')->insert([
                'task_id' => $task->id,
                'user_group_id' => $group->getKey(),
                'user_group_type' => $group::class,
            ]);
        }
    }
}
