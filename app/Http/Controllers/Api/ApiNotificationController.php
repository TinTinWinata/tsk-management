<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\NotificationController;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiNotificationController extends ApiController
{
    public function index() {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $notifications = $user->notifications()->select('id', 'title', 'type', 'created_at', 'updated_at')->get();
        return $this->sendResponse($notifications, "Succesfully send notifications");
    }
    public function reject(Notification $notification)
    {
        $user = Auth::user();
        $notification->delete();
        if($notification->type == "space_invitation" && isset($notification->sender_id)){
            NotificationController::createRejectSpaceNotification($user->name, $user->id);
        }
        return $this->sendResponse(null, "Succesfully rejected notification");
    }
    public function approve(Notification $notification)
    {
        Log::debug('Approve Notification : ', [$notification->type, $notification->meta_id]);
        if($notification->type == "space_invitation" && isset($notification->meta_id)){
            Log::debug('Approve Space Invitation : ', [$notification->meta_id]);
            $user = User::find(Auth::user()->id);
            $is_exists = $user->spaces()->where('space_id', $notification->meta_id)->exists();
            Log::debug('Is Exists : ', [$is_exists]);
            if($is_exists) {
                return $this->sendError("You already joined this space");
            }
            Log::debug('Attach!');
            $user->spaces()->attach($notification->meta_id);
        }
        $notification->delete();
        return $this->sendResponse(null, "Succesfully approved notification");
    }
}
