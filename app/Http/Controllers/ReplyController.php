<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reply; // ✅ ini WAJIB untuk bisa pakai Reply::create()

class ReplyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'discussion_id' => 'required|exists:discussions,id',
            'isi' => 'required'
        ]);

        Reply::create([
            'user_id' => Auth::id(),
            'discussion_id' => $request->discussion_id,
            'isi' => $request->isi
        ]);

        return redirect()->back()->with('success', 'Balasan berhasil dikirim.');
    }
}
