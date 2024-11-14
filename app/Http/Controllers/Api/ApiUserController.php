<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteRequest;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiUserController extends ApiController
{
    public function index()
    {
        $user = Auth::user();
        return $this->sendResponse($user, "Succesfully send user");
    }
}
