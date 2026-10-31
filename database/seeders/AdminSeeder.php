<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Administrator Account
        User::updateOrCreate(
            ['email' => 'admin@coworklibrary.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'administrator',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Clean up legacy superadmin record if present
        User::where('email', 'superadmin@coworklibrary.com')->delete();
    }
}
