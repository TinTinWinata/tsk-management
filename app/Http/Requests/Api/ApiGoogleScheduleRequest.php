<?php
namespace App\Http\Requests\Api;

class ApiGoogleScheduleRequest extends ApiRequest {
    public function rules(): array {
        return ['schedule_id' => 'required'];
    }
}

