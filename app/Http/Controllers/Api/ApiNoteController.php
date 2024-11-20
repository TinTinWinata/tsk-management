<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApiNoteRequest;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class ApiNoteController extends ApiController
{
    public function index()
    {
        $user = Auth::user();
        return $this->sendResponse($user->notes, "Succesfully send notes");
    }
    public function update(ApiNoteRequest $request, Note $note)
    {
        $data = $request->validated();
        $note->update($data);
        return $this->sendResponse($note, "Succesfully update notes");
    }
    public function delete(Note $note)
    {
        $note->delete();
        return $this->sendResponse($note, "Succesfully delete notes");
    }
    public function store(ApiNoteRequest $request){
        $user = Auth::user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $note = Note::create($data);
        return $this->sendResponse($note, "Succesfully create notes");
    }
}
