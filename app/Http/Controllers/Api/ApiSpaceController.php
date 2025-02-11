<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SpaceLogController;
use App\Http\Requests\Api\ApiSpaceRequest;
use App\Models\Space;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiSpaceController extends ApiController
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $spaces = $user->spaces()->with([
            'users' => function ($query) use ($user) {
                $query->where('users.id', '!=', $user->id);
            }
        ])->get();

        $spaces->each(function ($space) {
            $space->user_ids = $space->users->pluck('id');
            unset($space->users);
        });

        return $this->sendResponse($spaces, "Succesfully send spaces");
    }
    public function delete(Space $space)
    {
        $space->delete();
        return $this->sendResponse(null, "Succesfully delete spaces");
    }
    public function update(ApiSpaceRequest $request, Space $space)
    {
        $user = Auth::user();
        $data = $request->validated();
        $currentSpaceUserIds = array_flip($space->users()->select('users.id')->get()->pluck('id')->toArray());
        if($request->has('user_ids') && is_array($request->user_ids)){
            foreach($request->user_ids as $user_id) {
                if($user_id == null || $user_id == $user->id || isset($currentSpaceUserIds[$user_id])) {
                    continue;
                }
                NotificationController::createSpaceInvitationNotification($space, $user_id);
            }
        }
        $space->update($data);
        SpaceLogController::createSpaceLog($space->id, $user->name . " updated the space.", $user->id);
        return $this->sendResponse($space, "Succesfully update spaces");
    }
    public function schedules(Space $space) {
        $schedules = ScheduleController::getSchedules(ScheduleController::getDates(), $space->id);
        return $this->sendResponse($schedules, "Succesfully get schedules data");
    }
    public function store(ApiSpaceRequest $request){
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
                NotificationController::createSpaceInvitationNotification($space, $user_id);
            }
        }
        SpaceLogController::createSpaceLog($space->id, $user->name . " created the space.", $user->id);
        return $this->sendResponse($space, "Succesfully create spaces");
    }
}
