<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ApiUserController extends ApiController
{
    public function index()
    {
        $userId = Auth::user()->id;
        $users = User::where('id', '!=', $userId)->select('id', 'name', 'email')->get();
        return $this->sendResponse($users, "Succesfully send user");
    }
}
