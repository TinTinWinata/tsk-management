<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public static function createRejectSpaceNotification($user_name, $user_id) {
        $notification = new Notification();
        $notification->title = "Your space request has been rejected by " . $user_name;
        $notification->type = "space_rejec";
        $notification->user_id = $user_id;
        $notification->save();
    }
    public static function createSpaceInvitationNotification($space_name, $user_id, $sender_id)
    {
        $notification = new Notification();
        $notification->title = "You have been invited to join the space " . $space_name;
        $notification->type = "space_invitation";
        $notification->user_id = $user_id;
        $notification->save();
    }
}
