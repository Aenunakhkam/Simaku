<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Student::with('classroom.major')->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
        }

        if ($perPage === 'all') {
            $students = $query->paginate($query->count() > 0 ? $query->count() : 1);
        } else {
            $students = $query->paginate($perPage);
        }

        return Inertia::render('Students/Index', [
            'students' => $students->withQueryString(),
            'classrooms' => Classroom::with('major')->orderBy('level')->orderBy('name')->get(),
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students',
            'nis' => 'nullable|string|max:20|unique:students',
            'name' => 'required|string|max:255',
            'classroom_id' => 'nullable|exists:classrooms,id',
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
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status' => 'required|in:active,graduated,dropped_out',
        ]);

        $student->update($validated);

        return redirect()->back()->with('message', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function template()
    {
        $data = [
            ['NISN', 'NIS', 'Nama Lengkap', 'Nama Kelas', 'Status'],
            ['0012345678', '1001', 'Ahmad Dani', '10 RPL 1', 'Aktif'],
            ['0012345679', '1002', 'Budi Santoso', '10 TKJ 2', 'Aktif'],
        ];

        return SimpleXLSXGen::fromArray($data)->downloadAs('Template_Data_Siswa.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        if ($xlsx = SimpleXLSX::parse($request->file('file')->path())) {
            $rows = $xlsx->rows();
            
            // Skip the header row
            array_shift($rows);

            $importedCount = 0;

            foreach ($rows as $row) {
                // Ensure the row has enough columns
                if (count($row) < 4) continue;

                $nisn = trim($row[0]);
                $nis = trim($row[1]);
                $name = trim($row[2]);
                $className = isset($row[3]) ? trim($row[3]) : '';
                $status = isset($row[4]) && trim($row[4]) !== '' ? trim($row[4]) : 'Aktif';

                if (empty($nisn) || empty($name)) continue;

                $nis = $nis === '' ? null : $nis;

                $classroomId = null;
                if (!empty($className)) {
                    $classroom = Classroom::whereRaw('LOWER(name) = ?', [strtolower($className)])->first();
                    if ($classroom) {
                        $classroomId = $classroom->id;
                    }
                }

                // Create or Update student based on NISN
                Student::updateOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nis' => $nis,
                        'name' => $name,
                        'classroom_id' => $classroomId,
                        'status' => $status
                    ]
                );

                $importedCount++;
            }

            return redirect()->route('students.index')->with('success', "Berhasil mengimpor $importedCount data siswa.");
        } else {
            return redirect()->route('students.index')->with('error', 'Gagal membaca file Excel. ' . SimpleXLSX::parseError());
        }
    }
}
