<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        if (auth()->check()) {
            return Inertia::render('Note/Note', [
                'notes' => Auth::user()->notes
            ]);
        } else {
            return Inertia::render('Auth/Login');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate(['content' => 'required|max:1000', 'title' => 'required|max:255']);
        $data['user_id'] = $request->user()->id;
        Note::create($data);
        return Redirect::route('note');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required',
            'title' => 'required|max:255',
            'content' => 'required|max:1000',
        ]);

        $note->title = $request['title'];
        $note->content = $request['content'];

        $note->save();
        return Redirect::route('note');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return Redirect::route('note');
    }
}
