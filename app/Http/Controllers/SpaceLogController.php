<?php

namespace App\Http\Controllers;

use App\Models\SpaceLog;

class SpaceLogController extends Controller
{
    public static function createSpaceLog($space_id, $description, $user_id) {

        $space = SpaceLog::where('user_id', $user_id)->where('description', $description)->where('created_at', '>', now()->subMinutes(5))->first();
        if($space == null) {
            $spaceLog = new SpaceLog();
            $spaceLog->user_id = $user_id;
            $spaceLog->space_id = $space_id;
            $spaceLog->description = $description;
            $spaceLog->save();
            return true;
        }
        return false;
    }
}
