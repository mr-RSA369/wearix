<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'name' => 'Super Admin',
            'email' => 'superadmin@wearix.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);
    }
}
