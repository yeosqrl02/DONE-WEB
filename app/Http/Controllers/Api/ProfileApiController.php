<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileApiController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'      => 'sometimes|required|string|max:255',
            'email'     => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password'  => 'sometimes|nullable|string|min:8|confirmed',
            'phone'     => 'sometimes|nullable|string|max:20',
            'mobile'    => 'sometimes|nullable|string|max:20',
            'address'   => 'sometimes|nullable|string|max:255',
            'job_title' => 'sometimes|nullable|string|max:100',
            'location'  => 'sometimes|nullable|string|max:100',
            'website'   => 'sometimes|nullable|url|max:255',
            'github'    => 'sometimes|nullable|url|max:255',
            'twitter'   => 'sometimes|nullable|url|max:255',
            'instagram' => 'sometimes|nullable|url|max:255',
            'facebook'  => 'sometimes|nullable|url|max:255',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user' => $user,
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Akun berhasil dihapus',
        ]);
    }
}
