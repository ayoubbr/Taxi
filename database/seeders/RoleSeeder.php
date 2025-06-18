<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'SUPER_ADMIN'],
            ['name' => 'AGENCY_ADMIN'],
            ['name' => 'DRIVER'],
            ['name' => 'CLIENT'],
        ]);
    }
}
