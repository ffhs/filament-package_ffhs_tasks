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
            ->count(10)
            ->create();

        // My Tasks
        Task::factory()
            ->hasAttached($user)
            ->count(10)
            ->create();
        //
        // // Group tasks
        // Task::factory()
        //     ->hasAttached($group)
        //     ->count(10)
        //     ->create();
    }
}
