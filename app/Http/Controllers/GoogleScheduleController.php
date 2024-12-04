<?php

namespace App\Http\Controllers;

use App\Models\GoogleSchedule;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleScheduleController extends Controller
{
    public static function SyncSchedule(array $schedules) {
        $user = Auth::user();
        // !TODO: Validate User have Google Access Permission (etc. Google Login)

        foreach($schedules as $schedule) {
            $googleSchedule = GoogleSchedule::where('title', $schedule->title, 'date', $schedule->date)->first();
            if(!$googleSchedule) {
                GoogleSchedule::CreateNewGoogleSchedule($schedule->title, $schedule->date);
            }
        }
    }
    public static function CreateNewGoogleSchedule($title, $date) {
        $googleSchedule = new GoogleSchedule();
        $googleSchedule->title = $title;
        $googleSchedule->date = $date;

        // !TODO: Save to Google Calendar

        $googleSchedule->save();
    }
}
