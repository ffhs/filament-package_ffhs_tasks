<?php

namespace Database\Seeders;

use App\Models\User;
use Ffhs\FfhsTasks\Models\Task;
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
        //
        // // Group tasks
        // Task::factory()
        //     ->hasAttached($group)
        //     ->count(10)
        //     ->create();
    }
}
