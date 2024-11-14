<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteRequest;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiNoteController extends ApiController
{
    public function index()
    {
        $user = Auth::user();
        return $this->sendResponse($user->notes, "Succesfully send notes");
    }
    public function update(NoteRequest $request, Note $note)
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
    public function store(NoteRequest $request){
        $user = Auth::user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $note = Note::create($data);
        return $this->sendResponse($note, "Succesfully create notes");
    }
}
