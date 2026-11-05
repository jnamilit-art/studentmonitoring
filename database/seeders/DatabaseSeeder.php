<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // User::factory(10)->create();

        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'role' => 'parent',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            SuperAdminSeeder::class,
            SchoolDemoAccountsSeeder::class,
        ]);
    }
}
