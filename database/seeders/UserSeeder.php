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
        $clientRole = Role::where('name', 'client')->first();
        $operatorRole = Role::where('name', 'operator')->first();
        $driverRole = Role::where('name', 'driver')->first();
        $adminRole = Role::where('name', 'admin')->first();

        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'email' => 'admin@example.com',
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'role_id' => $adminRole->id,
            'user_type' => 'ADMIN', 
            'is_active' => true,
        ]);

        User::create([
            'username' => 'john.doe',
            'password' => Hash::make('password'),
            'email' => 'john.doe@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'role_id' => $clientRole->id,
            'user_type' => 'CLIENT',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'jane.driver',
            'password' => Hash::make('password'),
            'email' => 'jane.driver@example.com',
            'firstname' => 'Jane',
            'lastname' => 'Driver',
            'role_id' => $driverRole->id,
            'user_type' => 'DRIVER',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'another.driver',
            'password' => Hash::make('password'),
            'email' => 'another.driver@example.com',
            'firstname' => 'another',
            'lastname' => 'Driver',
            'role_id' => $driverRole->id,
            'user_type' => 'DRIVER',
            'is_active' => true,
        ]);

        User::create([
            'username' => 'mane.operator',
            'password' => Hash::make('password'),
            'email' => 'mane.operaton@example.com',
            'firstname' => 'Mane',
            'lastname' => 'Operator',
            'role_id' => $operatorRole->id,
            'user_type' => 'OPPERATOR',
            'is_active' => true,
        ]);
    }
}