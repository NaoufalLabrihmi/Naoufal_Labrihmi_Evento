<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define fake event types
        $eventTypes = [
            ['event_type_name' => 'Music Concert'],
            ['event_type_name' => 'Conference'],
            ['event_type_name' => 'Workshop'],
            ['event_type_name' => 'Exhibition'],
        ];

        // Insert fake data into the event_type table
        DB::table('event_type')->insert($eventTypes);
    }
}
