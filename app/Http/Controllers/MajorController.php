<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Major::latest();

        if ($search) {
            $query->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%");
        }

        if ($perPage === 'all') {
            $majors = $query->paginate($query->count() > 0 ? $query->count() : 1);
        } else {
            $majors = $query->paginate($perPage);
        }

        return Inertia::render('Majors/Index', [
            'majors' => $majors->withQueryString(),
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:majors',
            'name' => 'required|string|max:255',
        ]);

        Major::create($validated);

        return redirect()->back()->with('message', 'Jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:majors,code,' . $major->id,
            'name' => 'required|string|max:255',
        ]);

        $major->update($validated);

        return redirect()->back()->with('message', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Major $major)
    {
        $major->delete();

        return redirect()->back()->with('message', 'Jurusan berhasil dihapus.');
    }
}
