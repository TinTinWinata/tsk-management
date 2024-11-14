<?php

namespace App\Http\Controllers\Api;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiScheduleController extends ApiController
{
 public function save(Request $req)
    {
        $user = $req->user();
        $data = $req->all();
        $schedules = [];
        $i = 0;

        $startDate = null;
        $endDate = null;

        foreach ($data as $date => $detail) {
            if ($i === 0) {
                $startDate =  $date;
            }
            if ($i === count($data) - 1) {
                $endDate = $date;
            }
            foreach ($detail['schedules'] as $key => $schedule) {
                $schedule['id'] = Str::uuid();
                unset($schedule['created_at']);
                unset($schedule['updated_at']);
                $schedule['date'] = $date;
                $schedule['user_id'] = $user->id;
                $schedule['position'] = $key;
                array_push($schedules, $schedule);
            }
            $i++;
        }

        Schedule::where('user_id', $user->id)
            ->whereBetween(
                'date',
                [$startDate, $endDate]
            )
            ->delete();

        Schedule::insert($schedules);
        return response()->json('Succesfully saved new schedules', 200);
    }

}
