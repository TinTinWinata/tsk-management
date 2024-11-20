<?php
namespace App\Http\Requests\Api;
use App\Http\Requests\NoteRequest;

class ApiNoteRequest extends ApiRequest {
    public function rules(): array {
        $noteRequest = new NoteRequest();
        return $noteRequest->rules();
    }
}

