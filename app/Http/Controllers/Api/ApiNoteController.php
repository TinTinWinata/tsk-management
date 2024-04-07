<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiNoteController extends ApiController
{
    public function index()
    {
        $user = Auth::user();
        return $this->sendResponse($user->notes, "Succesfully send notes");
    }
}
