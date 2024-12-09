<?php
namespace App\Http\Requests\Api;

class ApiGoogleLoginRequest extends ApiRequest {
    public function rules(): array {
        return [
            'google_token' => 'required',
            'google_access_token' => 'required',
        ];
    }
}

