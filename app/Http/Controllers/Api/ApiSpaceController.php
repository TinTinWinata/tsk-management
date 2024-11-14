<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\NotificationController;
use App\Http\Requests\NoteRequest;
use App\Http\Requests\SpaceRequest;
use App\Models\Note;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
    public function store(SpaceRequest $request){
        $user = Auth::user();
        $data = $request->validated();
        $data['owner_id'] = $user->id;
        $space = Space::create($data);
        if($request->has('user_ids') && is_array($request->user_ids)){
            foreach($request->user_ids as $user_id) {
                NotificationController::createSpaceInvitationNotification($space->name, $user_id, $user->id);
            }
        }
        return $this->sendResponse($space, "Succesfully create spaces");
    }
}
