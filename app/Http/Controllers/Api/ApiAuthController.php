<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends ApiController
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return $this->sendResponse($token, 'Logged in successfully');
        } else {
            return $this->sendError("Invalid credentials");
        }
    }
    public function me(Request $request)
    {
        $userId = $request->user()->id;
        $user = User::find($userId)->with('notifications')->with('spaces')->get();
        return $this->sendResponse($user, "Succesfully send users");
    }
}
