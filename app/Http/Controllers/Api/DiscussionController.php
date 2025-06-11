<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DiscussionController extends Controller
{
    // Fungsi untuk mendapatkan semua diskusi beserta balasannya
    public function index()
    {
        $diskusi = Discussion::with(['user', 'replies.user'])->latest()->get();
        return response()->json($diskusi);
    }

    // Fungsi untuk membuat diskusi baru
    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string'
        ]);

        // Membuat diskusi baru
        $diskusi = Discussion::create([
            'user_id' => Auth::id(),
            'pertanyaan' => $request->pertanyaan
        ]);

        return response()->json([
            'message' => 'Pertanyaan berhasil dikirim.',
            'diskusi' => $diskusi
        ], 201);
    }

    // Fungsi untuk melihat satu diskusi tertentu
    public function show(Discussion $diskusi)
    {
        $diskusi->load(['user', 'replies.user']);
        return response()->json($diskusi);
    }

    // Fungsi untuk membuat balasan pada diskusi
    public function storeReply(Request $request, $diskusi_id)
    {
        // Validasi input balasan
        $request->validate([
            'isi' => 'required|string'
        ]);

        try {
            // Cek apakah diskusi dengan ID yang diberikan ada
            $diskusi = Discussion::findOrFail($diskusi_id);

            // Membuat balasan untuk diskusi yang ditemukan
            $balasan = Reply::create([
                'discussion_id' => $diskusi->id, // Mengaitkan balasan dengan diskusi
                'user_id' => Auth::id(), // ID pengguna yang membuat balasan
                'isi' => $request->isi // Isi dari balasan
            ]);

            return response()->json([
                'message' => 'Balasan berhasil dikirim.',
                'balasan' => $balasan
            ], 201);

        } catch (ModelNotFoundException $e) {
            // Jika diskusi tidak ditemukan, kembalikan respons 404
            return response()->json(['message' => 'Diskusi tidak ditemukan'], 404);
        }
    }
}
