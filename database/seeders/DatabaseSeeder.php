<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public static $DUMMY_USER_ID = '0c0a9c0b-0c49-46f4-80b0-31a68f8a2a78';
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        DB::table('users')->insert([
            'id' => self::$DUMMY_USER_ID,
            'name' => 'TinTin Winata',
            'email' => 'user@gmail.com',
            'password' => bcrypt('user')
        ]);

        $this->call([
            ScheduleSeeder::class,
            NoteSeeder::class
        ]);
    }
}
