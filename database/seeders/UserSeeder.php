<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $clientRole = Role::where('name', 'client')->first();
        $driverRole = Role::where('name', 'driver')->first();
        $operatorRole = Role::where('name', 'operator')->first();

        User::create([
            'username' => 'super.admin',
            'password' => Hash::make('password'),
            'email' => 'admin@email.com',
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'role_id' => $adminRole->id,
            'user_type' => 'ADMIN', 
            'is_active' => true,
        ]);

        User::create([
            'username' => 'john.client',
            'password' => Hash::make('password'),
            'email' => 'john.client@email.com',
            'firstname' => 'John',
            'lastname' => 'Client',
            'role_id' => $clientRole->id,
            'user_type' => 'CLIENT',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'alex.driver',
            'password' => Hash::make('password'),
            'email' => 'alex.driver@email.com',
            'firstname' => 'Alex',
            'lastname' => 'Driver',
            'role_id' => $driverRole->id,
            'user_type' => 'DRIVER',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'another.driver',
            'password' => Hash::make('password'),
            'email' => 'another.driver@email.com',
            'firstname' => 'Another',
            'lastname' => 'Driver',
            'role_id' => $driverRole->id,
            'user_type' => 'DRIVER',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'one.operator',
            'password' => Hash::make('password'),
            'email' => 'one.operator@email.com',
            'firstname' => 'One',
            'lastname' => 'Operator',
            'role_id' => $operatorRole->id,
            'user_type' => 'OPPERATOR',
            'is_active' => true,
        ]);
    }
}