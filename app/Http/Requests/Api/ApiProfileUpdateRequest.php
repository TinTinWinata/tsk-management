<?php
namespace App\Http\Requests\Api;
use App\Http\Requests\ProfileUpdateRequest;

class ApiProfileUpdateRequest extends ApiRequest {
    public function rules(): array {
        $profileUpdateRequest = new ProfileUpdateRequest();
        return $profileUpdateRequest->rules();
    }
}
