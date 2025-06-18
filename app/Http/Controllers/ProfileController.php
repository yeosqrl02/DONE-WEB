<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
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
     * Memperbarui data profil pengguna, termasuk avatar dan info sosial.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Ambil data yang tervalidasi, kecuali avatar
        $validated = $request->safe()->except('avatar');

        // Tangani upload avatar jika ada
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            $oldPath = public_path('storage/avatars/' . $user->avatar);
            if ($user->avatar && File::exists($oldPath)) {
                File::delete($oldPath);
            }

            // Simpan avatar baru ke direktori public
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Pastikan folder tujuan ada
            $destinationPath = public_path('storage/avatars');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Simpan file avatar
            $file->move($destinationPath, $filename);
            $user->avatar = $filename;
        }

        // Perbarui semua data kecuali avatar
        $user->fill($validated);

        // Jika email berubah, verifikasi ulang
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
