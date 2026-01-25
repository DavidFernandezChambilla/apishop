<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        $admin = User::create([
            'name' => 'Admin Shop',
            'email' => 'admin@shop.com',
            'password' => Hash::make('admin123'),
        ]);
        $admin->roles()->attach($adminRole);

        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('user123'),
        ]);
        $customer->roles()->attach($userRole);
    }
}
