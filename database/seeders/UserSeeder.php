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
        $superAdminRole = Role::where('name', 'SUPER_ADMIN')->first();
        $agencyAdminRole = Role::where('name', 'AGENCY_ADMIN')->first();
        $clientRole = Role::where('name', 'CLIENT')->first();
        $driverRole = Role::where('name', 'DRIVER')->first();

        User::create([
            'username' => 'super.admin',
            'password' => Hash::make('password'),
            'email' => 'admin@email.com',
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'role_id' => $superAdminRole->id,
            'is_active' => true,
        ]);

        User::create([
            'username' => 'agency.admin',
            'password' => Hash::make('password'),
            'email' => 'agency.admin@email.com',
            'firstname' => 'Agency',
            'lastname' => 'Admin',
            'role_id' => $agencyAdminRole->id,
            'is_active' => true,
        ]);

        User::create([
            'username' => 'john.client',
            'password' => Hash::make('password'),
            'email' => 'john.client@email.com',
            'firstname' => 'John',
            'lastname' => 'Client',
            'role_id' => $clientRole->id,
            'is_active' => true,
        ]);

        User::create([
            'username' => 'alex.driver',
            'password' => Hash::make('password'),
            'email' => 'alex.driver@email.com',
            'firstname' => 'Alex',
            'lastname' => 'Driver',
            'role_id' => $driverRole->id,
            'is_active' => true,
        ]);

        User::create([
            'username' => 'another.driver',
            'password' => Hash::make('password'),
            'email' => 'another.driver@email.com',
            'firstname' => 'Another',
            'lastname' => 'Driver',
            'role_id' => $driverRole->id,
            'is_active' => true,
        ]);
    }
}
