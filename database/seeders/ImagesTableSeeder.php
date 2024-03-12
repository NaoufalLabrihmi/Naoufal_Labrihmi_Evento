<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the number of images you want to seed
        $numImages = 57;

        // Loop to seed images
        for ($i = 50; $i <= $numImages; $i++) {
            // Fetch image URL from the API
            $imageUrl = 'https://source.unsplash.com/1000x1000/?rap';

            // Insert image URL into the database
            DB::table('event_images')->insert([
                'event_list_id' => $i, // Adjust this to match the event ID
                'event_image_path' => $imageUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
