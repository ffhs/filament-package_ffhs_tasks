<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\UserFactory;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (! User::where('email', 'dev@padmission.com')->exists()) {
            UserFactory::new()->create([
                'name' => 'Test User',
                'email' => 'dev@ffhs.ch',
            ]);
        }
    }
}
