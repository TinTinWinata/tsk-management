<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ApiNotificationController extends ApiController
{
    public function index() {
        $user = Auth::user();
        return $this->sendResponse($user->notifications, "Succesfully send notifications");
    }
    public function reject(Notification $notification)
    {
        $notification->delete();
        if($notification->type == "space_invitation" && isset($notification->sender_id)){

        }
        return $this->sendResponse(null, "Succesfully rejected notification");
    }
    public function approve(Notification $notification)
    {
        if($notification->type == "space_invitation" && isset($notification->meta_id)){
            $user = User::find(Auth::user()->id);
            $is_exists = $user->spaces()->where('space_id', $notification->meta_id)->exists();
            if($is_exists) {
                return $this->sendError("You already joined this space");
            }
            $user->spaces()->attach($notification->meta_id);
        }
        $notification->delete();
        return $this->sendResponse(null, "Succesfully approved notification");
    }
}
