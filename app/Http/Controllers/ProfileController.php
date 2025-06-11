<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Task;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil dan daftar tugas milik user yang sedang login.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        // Ambil tugas milik user ini yang belum selesai, urutkan berdasarkan tanggal
        $tasks = Task::where('user_id', $user->id)
            ->where('completed', false)
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('profile.index', [
            'user' => $user,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Memperbarui data profil pengguna, termasuk avatar.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Ambil data validasi dari FormRequest (kecuali avatar)
        $validated = $request->safe()->except('avatar');

        // Tangani upload avatar jika ada
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar && file_exists(public_path('storage/avatars/' . $user->avatar))) {
                unlink(public_path('storage/avatars/' . $user->avatar));
            }

            // Simpan avatar baru ke public/storage/avatars
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/avatars'), $filename);

            // Simpan nama file ke model
            $user->avatar = $filename;
        }

        // Perbarui data lainnya
        $user->fill($validated);

        // Reset email verifikasi jika email berubah
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
