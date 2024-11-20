<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ScheduleController;
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

        $start_date = null;
        $end_date = null;

        foreach ($data as $date => $detail) {
            if ($i === 0) {
                $start_date =  $date;
            }
            if ($i === count($data) - 1) {
                $end_date = $date;
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
            ->whereBetween('date', [$start_date, $end_date])
            ->delete();
        $model->schedules()->saveMany($schedules);
        return $this->sendResponse(count($schedules), "Succesfully saved schedules");
    }

}
