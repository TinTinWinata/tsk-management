<?php
namespace App\Http\Requests\Api;
use App\Http\Requests\SpaceRequest;

class ApiSpaceRequest extends ApiRequest {
    public function rules(): array {
        $spaceRequest = new SpaceRequest();
        return $spaceRequest->rules();
    }
}

