<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Space;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public static function createRejectSpaceNotification($user_name, $user_id) {
        $notification = new Notification();
        $notification->title = "Your space request has been rejected by " . $user_name;
        $notification->type = "space_reject";
        $notification->user_id = $user_id;
        $notification->save();
    }
    public static function createSpaceInvitationNotification(Space $space, $user_id, $sender_id)
    {
        $notification = new Notification();
        $notification->title = "You have been invited to join the space " . $space->name;
        $notification->type = "space_invitation";
        $notification->user_id = $user_id;
        $notification->meta_id = $space->id;
        $notification->save();
    }
}
