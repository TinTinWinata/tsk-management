<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Note::factory()->create([
            'user_id' => DatabaseSeeder::$DUMMY_USER_ID,
            'title' => 'Note 1',
            'content' => 'Note 1'
        ]);
    }
}
