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
                // Ambil semua siswa di jurusan ini
                $students = \App\Models\Student::where(function ($q) use ($major) {
                    $q->where('major_id', $major->id)
                      ->orWhereHas('classroom', function ($q2) use ($major) {
                          $q2->where('major_id', $major->id);
                      });
                })->withCount([
                    'billings as total_billings' => function ($query) use ($academicYearId) {
                        $query->where('academic_year_id', $academicYearId);
                    },
                    'billings as paid_billings' => function ($query) use ($academicYearId) {
                        $query->where('academic_year_id', $academicYearId)->where('is_paid', true);
                    }
                ])->with(['billings' => function ($query) use ($academicYearId) {
                    $query->where('academic_year_id', $academicYearId)
                          ->withSum('paymentDetails', 'amount');
                }])->get();

                $lunas = 0;
                $belumLunas = 0; // Nyicil / Baru bayar sebagian
                $belumBayar = 0;

                foreach ($students as $student) {
                    if ($student->total_billings > 0) {
                        if ($student->paid_billings == $student->total_billings) {
                            $lunas++;
                        } else {
                            // Cek apakah siswa ini sudah bayar cicilan sama sekali
                            $totalPaidAmount = $student->billings->sum('payment_details_sum_amount');
                            if ($totalPaidAmount > 0) {
                                $belumLunas++;
                            } else {
                                $belumBayar++;
                            }
                        }
                    } else {
                        // Jika tidak ada tagihan, kita anggap masuk belum bayar agar total siswa sesuai
                        $belumBayar++;
                    }
                }

                $total = $students->count();
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
        $academicYear = $academicYearId ? \App\Models\AcademicYear::find($academicYearId) : null;
        
        if (!$academicYear) {
            $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        }

        // Determine date range based on academic year name or query params
        $startDate = null;
        $endDate = null;
        
        if (request('all') !== 'true') {
            if (request('month') && request('year')) {
                $month = str_pad(request('month'), 2, '0', STR_PAD_LEFT);
                $year = request('year');
                $startDate = "{$year}-{$month}-01";
                $endDate = date('Y-m-t', strtotime($startDate));
            } elseif ($academicYear) {
                preg_match_all('/\d{4}/', $academicYear->name, $matches);
                if (count($matches[0]) >= 2) {
                    $startYear = intval($matches[0][0]);
                    $endYear = intval($matches[0][1]);
                    
                    if (str_contains(strtolower($academicYear->name), 'ganjil')) {
                        $startDate = "{$startYear}-07-01";
                        $endDate = "{$startYear}-12-31";
                    } elseif (str_contains(strtolower($academicYear->name), 'genap')) {
                        $startDate = "{$endYear}-01-01";
                        $endDate = "{$endYear}-06-30";
                    } else {
                        $startDate = "{$startYear}-07-01";
                        $endDate = "{$endYear}-06-30";
                    }
                }
            }
        }
        
        $paymentsQuery = \App\Models\Payment::with(['student.classroom.major', 'student.major', 'user']);
        $expensesQuery = \App\Models\Expense::with(['category', 'user']);

        if ($startDate && $endDate) {
            $paymentsQuery->whereBetween('date', [$startDate, $endDate]);
            $expensesQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $payments = $paymentsQuery->get();
        $expenses = $expensesQuery->get();
            
        $transactions = collect();
        
        foreach($payments as $p) {
            // Get Classroom & Major info
            $classroom = $p->student ? $p->student->classroom : null;
            $major = $p->student ? $p->student->major : null;
            if (!$major && $classroom) {
                $major = $classroom->major;
            }
            $kelasJurusan = '';
            if ($classroom) {
                $kelasJurusan .= $classroom->level . ' ' . $classroom->name;
            }
            if ($major) {
                $kelasJurusan .= ($kelasJurusan ? ' / ' : '') . $major->code;
            }
            if (empty($kelasJurusan)) {
                $kelasJurusan = '-';
            }

            $transactions->push((object)[
                'date' => $p->date,
                'no_bukti' => $p->invoice_number,
                'keterangan' => 'Penerimaan Tagihan - ' . ($p->student ? $p->student->name : ''),
                'kelas_jurusan' => $kelasJurusan,
                'debit' => $p->total_amount,
                'kredit' => 0,
            ]);
        }
        
        foreach($expenses as $e) {
            $transactions->push((object)[
                'date' => $e->date,
                'no_bukti' => $e->voucher_number ?? ('EXP-' . str_pad($e->id, 4, '0', STR_PAD_LEFT)),
                'keterangan' => 'Pengeluaran: ' . ($e->category ? $e->category->name : '') . ($e->note ? ' - ' . $e->note : ''),
                'kelas_jurusan' => '-',
                'debit' => 0,
                'kredit' => $e->amount,
            ]);
        }
        
        $transactions = $transactions->sortBy('date')->values();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bku_report', compact('transactions', 'academicYear'))
            ->setPaper('a4', 'landscape');
        
        $pdfName = "Buku_Kas_Umum_";
        if (request('all') === 'true') {
            $pdfName .= "Semua_Tahun";
        } elseif (request('month') && request('year')) {
            $pdfName .= request('year') . "_" . str_pad(request('month'), 2, '0', STR_PAD_LEFT);
        } else {
            $pdfName .= str_replace('/', '_', $academicYear ? $academicYear->name : 'All');
        }

        return $pdf->stream($pdfName . ".pdf");
    }
}
