<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileApiController extends Controller
{
    // Menampilkan profil pengguna
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }

    // Memperbarui profil pengguna
    public function update(Request $request)
    {
        $user = $request->user();

        // Validasi manual agar error JSON, bukan HTML
        $validator = Validator::make($request->all(), [
            'name'      => 'sometimes|required|string|max:255',
            'email'     => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'password'  => 'sometimes|nullable|string|min:8|confirmed',
            'phone'     => 'sometimes|nullable|string|max:20',
            'mobile'    => 'sometimes|nullable|string|max:20',
            'address'   => 'sometimes|nullable|string|max:255',
            'job_title' => 'sometimes|nullable|string|max:100',
            'location'  => 'sometimes|nullable|string|max:100',
            'website'   => 'sometimes|nullable|string|max:255',
            'github'    => 'sometimes|nullable|string|max:255',
            'twitter'   => 'sometimes|nullable|string|max:255',
            'instagram' => 'sometimes|nullable|string|max:255',
            'facebook'  => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Hash password jika dikirim
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'user' => $user,
        ]);
    }

    // Menghapus akun pengguna
    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Akun berhasil dihapus',
        ]);
    }

    // Upload avatar (tambahan opsional)
    public function uploadAvatar(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/avatars'), $filename);

            // Hapus avatar lama jika perlu
            if ($user->avatar && file_exists(public_path('storage/avatars/' . $user->avatar))) {
                unlink(public_path('storage/avatars/' . $user->avatar));
            }

            $user->avatar = $filename;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Avatar berhasil diunggah',
                'avatar' => asset('storage/avatars/' . $filename),
                'user' => $user,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengunggah avatar',
        ], 500);
    }
}
