<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        $majors = Major::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Majors/Index', [
            'majors' => $majors,
            'filters' => $request->only(['search'])
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
