<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Major;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::with('major')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhereHas('major', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                      });
            })
            ->orderBy('level')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Classrooms/Index', [
            'classrooms' => $classrooms,
            'majors' => Major::orderBy('name')->get(),
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'major_id' => 'required|exists:majors,id',
            'level' => 'required|integer|min:1|max:15',
            'name' => 'required|string|max:255',
        ]);

        Classroom::create($validated);

        return redirect()->back()->with('message', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'major_id' => 'required|exists:majors,id',
            'level' => 'required|integer|min:1|max:15',
            'name' => 'required|string|max:255',
        ]);

        $classroom->update($validated);

        return redirect()->back()->with('message', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return redirect()->back()->with('message', 'Kelas berhasil dihapus.');
    }
}
