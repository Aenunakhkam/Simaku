<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function classrooms()
    {
        $academicYearId = session('academic_year_id');

        // Ambil semua kelas beserta jumlah siswa dan hitung tagihan
        $classrooms = Classroom::with(['major'])->withCount([
            'students',
            'billings as total_billings' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            },
            'billings as paid_billings' => function ($query) use ($academicYearId) {
                $query->where('is_paid', true)->where('academic_year_id', $academicYearId);
            }
        ])->get();

        // Hitung persentase pembayaran per kelas
        $classrooms->map(function ($classroom) {
            if ($classroom->total_billings > 0) {
                $classroom->payment_percentage = round(($classroom->paid_billings / $classroom->total_billings) * 100, 1);
            } else {
                $classroom->payment_percentage = 0;
            }
            return $classroom;
        });

        return Inertia::render('Reports/Classrooms', [
            'classrooms' => $classrooms
        ]);
    }

    public function classroomDetail($id)
    {
        $classroom = Classroom::with(['major'])->findOrFail($id);
        $academicYearId = session('academic_year_id');

        $students = $classroom->students()->with([
            'billings' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)->with(['category', 'academicYear']);
            }
        ])->withCount([
            'billings as total_billings' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            },
            'billings as paid_billings' => function ($query) use ($academicYearId) {
                $query->where('is_paid', true)->where('academic_year_id', $academicYearId);
            }
        ])->get();

        $students->map(function ($student) {
            if ($student->total_billings > 0) {
                $student->payment_percentage = round(($student->paid_billings / $student->total_billings) * 100, 1);
            } else {
                $student->payment_percentage = 0;
            }
            return $student;
        });

        // Hitung rata-rata persentase kelas ini
        $totalBillings = $students->sum('total_billings');
        $paidBillings = $students->sum('paid_billings');
        $classroomPercentage = $totalBillings > 0 ? round(($paidBillings / $totalBillings) * 100, 1) : 0;

        return Inertia::render('Reports/ClassroomDetail', [
            'classroom' => $classroom,
            'students' => $students,
            'stats' => [
                'total_students' => $students->count(),
                'percentage' => $classroomPercentage,
                'total_billings' => $totalBillings,
                'paid_billings' => $paidBillings,
            ]
        ]);
    }
}
