<?php

namespace App\Http\Controllers\Api;

use App\Models\Space;
use App\Models\SpaceLog;
use Illuminate\Support\Facades\Auth;

class ApiSpaceLogController extends ApiController
{
    public function show($id)
    {
        $spaceLogs = SpaceLog::where('space_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        return $this->sendResponse($spaceLogs, "Succesfully send spaces");
    }
}
