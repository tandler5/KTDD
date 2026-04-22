<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'tandler12345@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'), // You might want to change this
                'role' => 'administrator',
            ]
        );
    }
}
