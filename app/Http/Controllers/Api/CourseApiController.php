<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CourseApiController extends Controller
{
    public function index()
    {
        $courses = Course::where('user_id', Auth::id())->get();
        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        try {
            $course = Course::create($validated);
            return response()->json([
                'message' => 'Mata kuliah berhasil ditambahkan.',
                'data' => $course
            ], 201);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan mata kuliah: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat menambah mata kuliah.'], 500);
        }
    }

    public function show($id)
    {
        $course = Course::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$course) {
            return response()->json(['error' => 'Mata kuliah tidak ditemukan.'], 404);
        }

        return response()->json($course);
    }

    public function update(Request $request, $id)
    {
        $course = Course::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$course) {
            return response()->json(['error' => 'Mata kuliah tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $course->update($validated);
            return response()->json(['message' => 'Mata kuliah berhasil diperbarui.', 'data' => $course]);
        } catch (\Exception $e) {
            Log::error('Gagal update mata kuliah: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memperbarui mata kuliah.'], 500);
        }
    }

    public function destroy($id)
    {
        $course = Course::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$course) {
            return response()->json(['error' => 'Mata kuliah tidak ditemukan.'], 404);
        }

        try {
            $course->delete();
            return response()->json(['message' => 'Mata kuliah berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus mata kuliah: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus mata kuliah.'], 500);
        }
    }
}
