<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ApiAuthController extends ApiController
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->sendError("Failed to do register", $validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('authToken')->plainTextToken;
        return $this->sendResponse($token, 'Logged in successfully');
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Failed to do login", $validator->errors(), 400);
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
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
        $user = User::where('id', $userId)->first();
        return $this->sendResponse($user, "Succesfully send users");
    }
}
