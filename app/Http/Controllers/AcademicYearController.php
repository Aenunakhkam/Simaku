<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicYear::latest();

        if ($request->search) {
            $query->where('name', 'ilike', "%{$request->search}%");
        }

        $academicYears = $query->paginate($request->per_page ?? 10)->withQueryString();

        return Inertia::render('AcademicYears/Index', [
            'academicYears' => $academicYears,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years',
            'is_active' => 'boolean',
        ]);

        if ($request->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        } else {
            // Default to false if not provided, but ensure at least one active exists?
            $validated['is_active'] = false;
        }

        AcademicYear::create($validated);

        return redirect()->back()->with('message', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name,' . $academicYear->id,
            'is_active' => 'boolean',
        ]);

        if ($request->is_active) {
            AcademicYear::where('is_active', true)->where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update($validated);

        return redirect()->back()->with('message', 'Tahun ajaran berhasil diperbarui.');
    }
    
    public function setActive(AcademicYear $academicYear)
    {
        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);
        
        return redirect()->back()->with('message', 'Tahun ajaran berhasil diaktifkan secara global.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return redirect()->back()->with('error', 'Tahun ajaran aktif tidak dapat dihapus.');
        }

        $academicYear->delete();
        return redirect()->back()->with('message', 'Tahun ajaran berhasil dihapus.');
    }
}
