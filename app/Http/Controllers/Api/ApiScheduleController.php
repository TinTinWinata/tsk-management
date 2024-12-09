<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ScheduleController;
use App\Models\GoogleSchedule;
use App\Models\Schedule;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiScheduleController extends ApiController
{
    public function index() {
        $schedules = ScheduleController::getSchedules(ScheduleController::getDates());
        return $this->sendResponse($schedules, "Succesfully get schedules data");
    }
    public function save(Request $req) {
        $model = $req->user();
        if($req->has('space_id')) {
            $space = Space::find($req->get('space_id'));
            if($space) {
                $model = $space;
            }
        }
        $data = $req->except('space_id');
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
                $new_schedule = new Schedule();
                $new_schedule->fill($schedule);
                $new_schedule->id = Str::uuid();
                $new_schedule->date = $date;
                $new_schedule->position = $key;
                array_push($schedules, $new_schedule);
            }
            $i++;
        }

        $model->schedules()
            ->whereBetween('date', [$startDate, $endDate])
            ->delete();

        $model->schedules()->saveMany($schedules);

        if($req->has('space_id') == false && $model->is_sync_google) {
            $this->syncGoogle($schedules, $startDate, $endDate, $model);
        }

        return $this->sendResponse(count($schedules), "Succesfully saved schedules");
    }

    private function syncGoogle(array $schedules, $startDate, $endDate, $user){
        $deletedGoogleSchedules = GoogleSchedule::where('user_id', $user->id)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->whereNotIn('schedule_id', array_map(function($schedule) {
                return $schedule->id;
            }, $schedules))
            ->get();
        foreach($schedules as $schedule) {
            ApiGoogleScheduleController::setAccessToken($user);
            ApiGoogleScheduleController::sync($schedule, $user);
        }
        foreach($deletedGoogleSchedules as $deletedGoogleSchedule) {
            ApiGoogleScheduleController::setAccessToken($user);
            ApiGoogleScheduleController::delete($deletedGoogleSchedule);
        }
    }
}
