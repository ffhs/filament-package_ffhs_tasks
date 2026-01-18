<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
