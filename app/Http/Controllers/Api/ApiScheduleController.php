<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SpaceLogController;
use App\Models\GoogleSchedule;
use App\Models\Schedule;
use App\Models\Space;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiScheduleController extends ApiController
{
    public function storeAi(Request $request){
        $text = $request->get('text');
        $response = Http::post(env('API_AI_BACKEND') . '/api/predict', [
            'text' => $text,
        ]);
        if ($response->successful()) {
            $user = $request->user();
            $data =   $response->json();
            $date = $data['date'];
            $intent = $data['intent'];
            if($date && $intent) {
                $schedule = $user->schedules()->create([
                    'date' => $date,
                    'title' => $intent,
                    'is_done' => false,
                    'position' => 0
                ]);
            Log::debug('[Created Schedule From AI]', [$schedule]);
            try{
                if($user->is_sync_google) {
                    ApiGoogleScheduleController::setAccessToken($user);
                    ApiGoogleScheduleController::create($schedule, $user);
                }
            } catch(Exception $err) {
                Log::error('[Error Create Google]', [$err]);
            }
            return $this->sendResponse($schedule, "Succesfully created schedule");
           }
        }
        return $this->sendError(null, [], 500);
    }
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

        Log::debug('Inserted schedules', [$schedules]);
        Log::debug('Total Inserted Schedules', [count($schedules)]);

        $deletedSchedules = $model->schedules()
            ->whereBetween('date', [$startDate, $endDate])
            ->delete();
        Log::debug('Deleted schedules', [$deletedSchedules]);

        $model->schedules()->saveMany($schedules);

        if($req->has('space_id')) {
            SpaceLogController  ::createSpaceLog($model->id, $req->user()->name . " updating space schedules.", $req->user()->id);
        }

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
