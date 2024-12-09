<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApiProfileUpdateRequest;
use App\Http\Requests\Api\ApiUpdatePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ApiUserController extends ApiController
{
    public function updateProfile(ApiProfileUpdateRequest $request) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $request->validated();
        $user->update($data);
        return $this->sendResponse($this->getCurrentUser(), "Succesfully update user");
    }

    public function updateIsSyncGoogle(Request $request) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        Log::debug('Request : ', $request->all());
        $user->update([
            'is_sync_google' => $request->get('is_sync_google')
        ]);
        return $this->sendResponse($this->getCurrentUser(), "Succesfully update is sync google");
    }

    public function updatePassword(ApiUpdatePasswordRequest $request) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $request->validated();
        if (!Hash::check($data['old_password'], $user->password)) {
            return $this->sendError("Old password is wrong", ['old_password' => ['Invalid old pasword']], 400);
        }
        $user->update([
            'password' => bcrypt($data['new_password'])
        ]);
        return $this->sendResponse($this->getCurrentUser(), "Succesfully update password");
    }

    public function getCurrentUser() {
        $user = User::where('id', Auth::user()->id)->select('id', 'name', 'email', 'photo_profile', 'is_sync_google')->first();
        return $user;
    }

    public function index()
    {
        $userId = Auth::user()->id;
        $users = User::where('id', '!=', $userId)->select('id','is_sync_google', 'name', 'email', 'photo_profile')->get();
        return $this->sendResponse($users, "Succesfully send user");
    }
}
