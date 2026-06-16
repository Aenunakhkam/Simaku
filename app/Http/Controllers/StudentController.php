<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('classroom.major')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhereHas('classroom', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Students/Index', [
            'students' => $students,
            'classrooms' => Classroom::with('major')->orderBy('level')->orderBy('name')->get(),
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students',
            'nis' => 'nullable|string|max:20|unique:students',
            'name' => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'status' => 'required|in:active,graduated,dropped_out',
        ]);

        Student::create($validated);

        return redirect()->back()->with('message', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $student->id,
            'nis' => 'nullable|string|max:20|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'status' => 'required|in:active,graduated,dropped_out',
        ]);

        $student->update($validated);

        return redirect()->back()->with('message', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->back()->with('message', 'Data siswa berhasil dihapus.');
    }
}
