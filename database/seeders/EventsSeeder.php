<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define an array of event data
        $events = [];

        // Loop to create 8 events
        for ($i = 1; $i <= 8; $i++) {
            $events[] = [
                'event_name' => 'Event ' . $i,
                'event_start_date' => Carbon::now()->addDays($i),
                'event_start_time' => '09:00:00',
                'event_end_date' => Carbon::now()->addDays($i),
                'event_end_time' => '17:00:00',
                'event_location' => rand(8, 11), // Replace with actual city ID range
                'event_address' => '123 Main St',
                'event_slug' => Str::slug('Event ' . $i),
                'event_author_id' => rand(1, 4), // Replace with actual author ID range
                'event_guestCapacity' => 100,
                'event_subscription' => 'P',
                'event_ticket_price' => '10.00',
                'event_status' => 1,
                'approved' => 1,
                'event_description' => 'Description of Event ' . $i,
                'event_reservation_method' => 'automatic',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'event_type_id' => rand(1, 5)
            ];
        }

        // Insert events into the database
        DB::table('events')->insert($events);
    }
}
