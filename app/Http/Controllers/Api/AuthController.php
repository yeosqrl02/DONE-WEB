<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    // 📝 Register User baru
    public function register(Request $request)
    {
        // Validasi input dari user
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Membuat user baru
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Membuat token untuk user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Response registrasi
        return response()->json([
            'message' => 'User berhasil didaftarkan',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'token' => $token, // ✅ tambahan agar Flutter bisa akses langsung
        ], 201);
    }

    // 🔐 Login user
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Cek user dan password
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Token login
        $token = $user->createToken('auth_token')->plainTextToken;

        // Response login
        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'token' => $token, // ✅ ditambahkan agar Flutter login sukses
        ]);
    }

    // 🚪 Logout (hapus token aktif)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
