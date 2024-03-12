<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cities;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define cities data
        $cities = [
            ['name' => 'New York'],
            ['name' => 'Los Angeles'],
            ['name' => 'Chicago'],
            ['name' => 'London']
            // Add more cities as needed
        ];

        // Insert cities into the database
        foreach ($cities as $city) {
            Cities::create($city);
        }
    }
}
