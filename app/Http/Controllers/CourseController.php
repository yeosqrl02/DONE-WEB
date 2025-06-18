<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    /**
     * Simpan mata kuliah baru milik user login.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        try {
            Course::create($validated);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan mata kuliah: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'error' => 'Terjadi kesalahan saat menambah mata kuliah.'
            ]);
        }

        return redirect()->back()->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit mata kuliah (opsional jika tidak pakai AJAX/modal).
     */
    public function edit(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit mata kuliah ini.');
        }

        return view('courses.edit', compact('course'));
    }

    /**
     * Update nama mata kuliah milik user.
     */
    public function update(Request $request, Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui mata kuliah ini.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $course->update($validated);
        } catch (\Exception $e) {
            Log::error('Gagal update mata kuliah: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'error' => 'Gagal memperbarui mata kuliah.'
            ]);
        }

        return redirect()->back()->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Hapus mata kuliah milik user.
     */
    public function destroy(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus mata kuliah ini.');
        }

        try {
            $course->delete();
        } catch (\Exception $e) {
            Log::error('Gagal menghapus mata kuliah: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'error' => 'Gagal menghapus mata kuliah.'
            ]);
        }

        return redirect()->back()->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
