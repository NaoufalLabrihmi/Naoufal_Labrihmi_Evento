<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'name' => 'Admin Name',
            'email' => 'admin@example.com',
            'contact' => '1234567890',
            'address' => 'Admin Address',
            'website' => 'adminwebsite.com',
            'facebook' => 'adminfacebook',
            'instagram' => 'admininstagram',
            'twitter' => 'admintwitter',
            'youtube' => 'adminyoutube',
            'password' => Hash::make('123456'),
            'user_type' => 'A',
            'image' => 'admin.jpg',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
