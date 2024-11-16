<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Requests\NoteRequest;
use App\Http\Requests\SpaceRequest;
use App\Models\Note;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiSpaceController extends ApiController
{
    public function index()
    {
        $user = Auth::user();
        return $this->sendResponse($user->spaces, "Succesfully send spaces");
    }
    public function delete(Space $space)
    {
        $space->delete();
        return $this->sendResponse(null, "Succesfully delete spaces");
    }
    public function update(SpaceRequest $request, Space $space)
    {
        $data = $request->validated();
        $space->update($data);
        return $this->sendResponse($space, "Succesfully update spaces");
    }
    public function schedules(Space $space) {
        $schedules = ScheduleController::getSchedules(ScheduleController::getDates(), $space->id);
        return $this->sendResponse($schedules, "Succesfully get schedules data");
    }
    public function store(SpaceRequest $request){
        $user = Auth::user();
        $data = $request->validated();
        $data['owner_id'] = $user->id;
        $space = Space::create($data);
        $space->users()->attach($user->id);
        if($request->has('user_ids') && is_array($request->user_ids)){
            foreach($request->user_ids as $user_id) {
                if($user_id == $user->id) {
                    continue;
                }
                NotificationController::createSpaceInvitationNotification($space->name, $user_id, $user->id);
            }
        }
        return $this->sendResponse($space, "Succesfully create spaces");
    }
}
