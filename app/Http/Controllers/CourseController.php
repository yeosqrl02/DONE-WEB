<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Task;

class CourseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:courses,name'
        ]);

        Course::create($request->only('name'));
        return redirect()->route('tasks')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(Course $course)
    {
        $tasks = Task::with('course')->get();
        $courses = Course::all();

        return view('tasks.index', [
            'tasks' => $tasks,
            'courses' => $courses,
            'editCourse' => $course
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|unique:courses,name,' . $course->id
        ]);

        $course->update($request->only('name'));
        return redirect()->route('tasks')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('tasks')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
