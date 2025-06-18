<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $cities = [
            ['name' => 'Marrakech'],
            ['name' => 'Casablanca'],
            ['name' => 'Rabat'],
            ['name' => 'Safi'],
            ['name' => 'Essaouira'],
            ['name' => 'Agadir'],
            // ...ajoutez toutes les autres villes ici
        ];
        
        DB::table('cities')->insert($cities);
       
    }
}
