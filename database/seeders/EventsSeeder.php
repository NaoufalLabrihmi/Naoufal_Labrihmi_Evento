<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Events;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfEvents = 20;

        for ($i = 1; $i <= $numberOfEvents; $i++) {
            Events::create([
                'event_name' => 'Event ' . $i,
                'event_start_date' => now()->addDays($i),
                'event_start_time' => '09:00', // Example start time
                'event_end_date' => now()->addDays($i + 2),
                'event_end_time' => '17:00', // Example end time
                'event_location' => 'Location ' . $i,
                'event_address' => 'Address ' . $i,
                'event_slug' => Str::slug('Event ' . $i),
                'event_author_id' => '1', // Assuming event author ID is 1 for simplicity
                'event_guestCapacity' => rand(50, 200), // Example guest capacity
                'event_subscription' => $i % 2 == 0 ? 'F' : 'P', // Alternating between F: Free and P: Paid
                'event_ticket_price' => $i % 2 == 0 ? null : rand(10, 100), // Random ticket price for paid events
                'event_status' => 1,
                'event_description' => 'Description for Event ' . $i,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
