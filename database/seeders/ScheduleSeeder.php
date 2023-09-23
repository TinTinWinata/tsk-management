<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schedule::factory()->create([
            'user_id' => '1',
            'date' => Carbon::now(),
            'title' => 'Scheduling TPA',
            'is_done' => false
        ]);
    }
}
