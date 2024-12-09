<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApiGoogleLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ApiAuthController extends ApiController
{
    public function googleAuth(ApiGoogleLoginRequest $request) {
        $data = $request->validated();
        $googleToken = $data['google_token'];
        $googleAccessToken = $data['google_access_token'];

        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $googleToken,
        ]);

        if ($response->failed()) {
            return $this->sendError("Failed to do google login", [], 400);
        }

        $googleUser = $response->json();

        if ($googleUser['aud'] !== env('GOOGLE_CLIENT_ID')) {
            return $this->sendError("Failed to do google login", [], 400);
        }

        $email = $googleUser['email'] ?? null;
        $name = $googleUser['name'] ?? null;
        $picture = $googleUser['picture'] ?? null;

        if(!$email || !$name) {
            return $this->sendError("Failed to do google login", [], 400);
        }

        $user = User::where('email', $email)->first();
        if(!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'photo_profile' => $picture,
                'password' => Hash::make('S4Cr4t!!'),
                'google_token' => $googleToken,
                'google_access_token' => $googleAccessToken,
            ]);
        } else {
            User::where('id', $user->id)->update([
                'google_token' => $googleToken,
                'google_access_token' => $googleAccessToken, 
            ]);
        }
        $user->last_login = now();
        $token = $user->createToken('authToken')->plainTextToken;
        return $this->sendResponse($token, 'Logged in successfully');
    }
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
        $user->last_login = now();
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
            $user->last_login = now();
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
