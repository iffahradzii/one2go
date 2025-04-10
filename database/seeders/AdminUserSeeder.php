<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);
    }
}
