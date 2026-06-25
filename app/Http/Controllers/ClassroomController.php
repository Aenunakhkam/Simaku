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
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Classroom::with('major')
            ->when($search, function ($query, $search) {
                $query->where('name', 'ilike', "%{$search}%")
                      ->orWhereHas('major', function ($q) use ($search) {
                        $q->where('name', 'ilike', "%{$search}%")
                            ->orWhere('code', 'ilike', "%{$search}%");
                      });
            })
            ->orderBy('level')
            ->orderBy('name');

        if ($perPage === 'all') {
            $classrooms = $query->paginate($query->count() > 0 ? $query->count() : 1);
        } else {
            $classrooms = $query->paginate($perPage);
        }

        return Inertia::render('Classrooms/Index', [
            'classrooms' => $classrooms->withQueryString(),
            'majors' => Major::orderBy('name')->get(),
            'filters' => $request->only(['search', 'per_page'])
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
