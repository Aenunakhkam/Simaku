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

    public function majors(Request $request)
    {
        $academicYearId = session('academic_year_id');
        if (!$academicYearId) {
            return redirect()->route('academic-years.index')->with('error', 'Silakan pilih tahun ajaran aktif terlebih dahulu.');
        }

        // Hitung statistik berdasarkan jurusan
        $majorStats = \App\Models\Major::select('majors.id', 'majors.name', 'majors.code')
            ->get()
            ->map(function ($major) use ($academicYearId) {
                $billings = \App\Models\Billing::whereHas('student', function ($q) use ($major) {
                    $q->where('major_id', $major->id)
                      ->orWhereHas('classroom', function ($q2) use ($major) {
                          $q2->where('major_id', $major->id);
                      });
                })->where('academic_year_id', $academicYearId)
                ->withSum('paymentDetails', 'amount')
                ->get();

                $lunas = 0;
                $belumLunas = 0; // Nyicil
                $belumBayar = 0;

                foreach ($billings as $b) {
                    if ($b->is_paid) {
                        $lunas++;
                    } else {
                        $paidAmount = $b->payment_details_sum_amount ?? 0;
                        if ($paidAmount > 0) {
                            $belumLunas++;
                        } else {
                            $belumBayar++;
                        }
                    }
                }

                $total = $billings->count();
                $percentage = $total > 0 ? round(($lunas / $total) * 100, 1) : 0;

                return [
                    'id' => $major->id,
                    'name' => $major->name,
                    'code' => $major->code,
                    'lunas' => $lunas,
                    'belum_lunas' => $belumLunas,
                    'belum_bayar' => $belumBayar,
                    'total' => $total,
                    'percentage' => $percentage
                ];
            });

        return Inertia::render('Reports/Majors', [
            'majors' => $majorStats
        ]);
    }

    public function majorDetail($id)
    {
        $major = \App\Models\Major::findOrFail($id);
        $academicYearId = session('academic_year_id');

        $students = \App\Models\Student::with(['classroom'])->where(function ($q) use ($major) {
            $q->where('major_id', $major->id)
              ->orWhereHas('classroom', function ($q2) use ($major) {
                  $q2->where('major_id', $major->id);
              });
        })->with([
            'billings' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)->with(['category', 'academicYear']);
            }
        ])->withCount([
            'billings as total_billings' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            },
            'billings as paid_billings' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)->where('is_paid', true);
            }
        ])->get()->map(function ($student) {
            $student->payment_percentage = $student->total_billings > 0 
                ? round(($student->paid_billings / $student->total_billings) * 100) 
                : 0;
            return $student;
        });

        $totalBillings = $students->sum('total_billings');
        $paidBillings = $students->sum('paid_billings');
        $majorPercentage = $totalBillings > 0 ? round(($paidBillings / $totalBillings) * 100, 1) : 0;

        return Inertia::render('Reports/MajorDetail', [
            'major' => $major,
            'students' => $students,
            'stats' => [
                'total_students' => $students->count(),
                'percentage' => $majorPercentage,
                'total_billings' => $totalBillings,
                'paid_billings' => $paidBillings,
            ]
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

    public function printClassroomPdf($id)
    {
        $classroom = Classroom::with(['major'])->findOrFail($id);
        $academicYearId = session('academic_year_id');

        $students = $classroom->students()->with([
            'billings' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)->with(['category']);
            }
        ])->get();

        $students->map(function ($student) {
            $student->total_billings = $student->billings->sum('amount');
            $student->paid_billings = $student->billings->where('is_paid', true)->sum('amount');
            return $student;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.classroom_report', compact('classroom', 'students'))
            ->setPaper('a4', 'landscape');
        
        return $pdf->download("laporan_kelas_{$classroom->level}_{$classroom->name}.pdf");
    }

    public function printStudentPdf($id)
    {
        $student = \App\Models\Student::with(['classroom.major'])->findOrFail($id);
        $academicYearId = session('academic_year_id');

        $billings = $student->billings()
            ->where('academic_year_id', $academicYearId)
            ->with(['category', 'paymentDetails.payment'])
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.student_report', compact('student', 'billings'))
            ->setPaper('a4', 'landscape');
        
        return $pdf->download("laporan_keuangan_{$student->name}.pdf");
    }

    public function printBkuPdf()
    {
        $academicYearId = session('academic_year_id');
        $academicYear = \App\Models\AcademicYear::find($academicYearId);
        
        $payments = \App\Models\Payment::with(['student', 'user'])->get();
            
        $expenses = \App\Models\Expense::with(['category', 'user'])->get();
            
        $transactions = collect();
        
        foreach($payments as $p) {
            $transactions->push((object)[
                'date' => $p->date,
                'no_bukti' => $p->invoice_number,
                'keterangan' => 'Penerimaan Tagihan - ' . ($p->student ? $p->student->name : ''),
                'debit' => $p->total_amount,
                'kredit' => 0,
            ]);
        }
        
        foreach($expenses as $e) {
            $transactions->push((object)[
                'date' => $e->date,
                'no_bukti' => 'EXP-' . str_pad($e->id, 4, '0', STR_PAD_LEFT),
                'keterangan' => 'Pengeluaran: ' . $e->description,
                'debit' => 0,
                'kredit' => $e->amount,
            ]);
        }
        
        $transactions = $transactions->sortBy('date')->values();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bku_report', compact('transactions', 'academicYear'))
            ->setPaper('a4', 'landscape');
        
        return $pdf->stream("Buku_Kas_Umum_" . str_replace('/', '_', $academicYear ? $academicYear->name : 'All') . ".pdf");
    }
}
