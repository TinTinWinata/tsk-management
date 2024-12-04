<?php
namespace App\Http\Requests\Api;

use Illuminate\Validation\Rules\Password;

class ApiUpdatePasswordRequest extends ApiRequest {
    public function rules(): array {
        return [
            'old_password' => 'required|string',
            'new_password' => ['required', Password::defaults()],
            'confirm_password' => 'required|string|min:6|same:new_password',
        ];
    }
}

