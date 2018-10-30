<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Note;
use Illuminate\Http\Request;
use App\Traits\MemberUtility;

class NoteController extends Controller
{
    use MemberUtility;
    
    public function getLatest($doctor_id, $member_id)
    {
        $note = Note
            ::with('doctor', 'member')
            ->where('doctors_id', $doctor_id)
            ->where('members_id', $member_id)
            ->orderBy('created_at', 'DESC')
            ->first();
        if ($note) {
            return response()->json($note);
        }

        return response()->json(new Note([
            'doctors_id' => $doctor_id,
            'members_id' => $member_id,
            'note' => '',
        ]));
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'doctors_id' => ['required', 'exists:doctors,users_id'],
            'members_id' => ['required', 'exists:users,id'],
            'note' => ['nullable', 'string'],
        ]);
        $note = $request->input('note');
        $members_id = $request->input('members_id');
        $doctors_id = $request->input('doctors_id');

        if(!$this->isVIPMember($members_id, $doctors_id))  {
            return response()->json('invalid member', 404);
        }
        if (empty($note)) {
            $note = '';
        }
        $note = Note::create([
            'doctors_id' => $request->input('doctors_id'),
            'members_id' => $request->input('members_id'),
            'note' => $note,
        ]);

        return response()->json($note);
    }
}