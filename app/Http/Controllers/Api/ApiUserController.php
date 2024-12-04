<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApiProfileUpdateRequest;
use App\Http\Requests\Api\ApiUpdatePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiUserController extends ApiController
{
    public function updateProfile(ApiProfileUpdateRequest $request) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $request->validated();
        $user->update($data);
        return $this->sendResponse($user, "Succesfully update user");
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
        return $this->sendResponse($user, "Succesfully update password");
    }

    public function index()
    {
        $userId = Auth::user()->id;
        $users = User::where('id', '!=', $userId)->select('id', 'name', 'email')->get();
        return $this->sendResponse($users, "Succesfully send user");
    }
}
