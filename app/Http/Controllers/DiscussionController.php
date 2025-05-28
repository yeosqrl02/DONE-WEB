<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Discussion;

class DiscussionController extends Controller
{
    public function index() {
        $discussions = Discussion::with(['user', 'replies.user'])->latest()->get();
        return view('discussion.index', compact('discussions'));
    }

    public function store(Request $request) {
        $request->validate([
            'pertanyaan' => 'required'
        ]);

        Discussion::create([
            'user_id' => Auth::id(),
            'pertanyaan' => $request->pertanyaan
        ]);

        return redirect()->back()->with('success', 'Pertanyaan berhasil dikirim.');
    }
}
