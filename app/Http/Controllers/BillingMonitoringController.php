<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $academicYearId = session('academic_year_id');
        
        $query = Student::with([
            'classroom', 
            'classroom.major',
            'billings' => function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId)
                  ->with(['category', 'academicYear'])
                  ->withSum('paymentDetails', 'amount');
            },
            'payments' => function($q) use ($academicYearId) {
                $q->whereHas('paymentDetails.billing', function($sub) use ($academicYearId) {
                    $sub->where('academic_year_id', $academicYearId);
                })->with(['paymentDetails.billing.category', 'user'])->latest('id');
            }
        ])
            ->withSum(['billings' => function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            }], 'amount')
            ->withSum(['paymentDetails' => function($q) use ($academicYearId) {
                $q->where('billings.academic_year_id', $academicYearId);
            }], 'amount');

        // Filter by classroom if requested
        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('nisn', 'ilike', '%' . $request->search . '%');
            });
        }

        // Filter by payment status
        if ($request->filled('status')) {
            $status = $request->status;
            $billingSumSql = '(SELECT COALESCE(SUM(amount), 0) FROM billings WHERE billings.student_id = students.id AND billings.academic_year_id = ?)';
            $paymentSumSql = '(SELECT COALESCE(SUM(pd.amount), 0) FROM payment_details pd INNER JOIN billings b ON pd.billing_id = b.id WHERE b.student_id = students.id AND b.academic_year_id = ?)';
            
            if ($status === 'lunas') {
                $query->whereRaw("{$billingSumSql} = 0 OR {$paymentSumSql} >= {$billingSumSql}", [$academicYearId, $academicYearId, $academicYearId]);
            } elseif ($status === 'belum_lunas') { // nyicil
                $query->whereRaw("{$paymentSumSql} > 0 AND {$paymentSumSql} < {$billingSumSql}", [$academicYearId, $academicYearId, $academicYearId]);
            } elseif ($status === 'belum_bayar') {
                $query->whereRaw("{$billingSumSql} > 0 AND {$paymentSumSql} = 0", [$academicYearId, $academicYearId]);
            }
        }

        // Fetch all raw to calculate the statuses for the cards (in a real big app this might need raw SQL grouping, but for school app it's fine)
        // Wait, to get the total counts without fetching all, we can use raw SQL counts or just fetch all and reduce in PHP.
        // Given typically < 2000 students, PHP reduction is fast enough, but let's do it via DB for performance.

        // Actually, we need to return pagination.
        // Let's get the paginated result first.
        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') {
            $students = $query->paginate(10000)->withQueryString();
        } else {
            $students = $query->paginate($perPage)->withQueryString();
        }

        // Get global counts safely using withSum without retrieving all heavy relations
        $allStudentsStats = Student::where(function($q) use ($request) {
            if ($request->filled('classroom_id')) {
                $q->where('classroom_id', $request->classroom_id);
            }
            if ($request->filled('search')) {
                $q->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('nisn', 'ilike', '%' . $request->search . '%');
            }
        })
        ->withSum(['billings' => function($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        }], 'amount')
        ->withSum(['paymentDetails' => function($q) use ($academicYearId) {
            $q->where('billings.academic_year_id', $academicYearId);
        }], 'amount')
        ->get(['id']);

        $stats = [
            'lunas' => 0,
            'nyicil' => 0,
            'belum_bayar' => 0,
        ];

        foreach ($allStudentsStats as $s) {
            $totalBill = (float) ($s->billings_sum_amount ?? 0);
            $totalPaid = (float) ($s->payment_details_sum_amount ?? 0);

            if ($totalBill == 0) {
                // Ignore or count as lunas? Let's count as lunas
                $stats['lunas']++;
            } else if ($totalPaid >= $totalBill) {
                $stats['lunas']++;
            } else if ($totalPaid > 0 && $totalPaid < $totalBill) {
                $stats['nyicil']++;
            } else {
                $stats['belum_bayar']++;
            }
        }

        // Add the computed status to each paginated item
        $students->getCollection()->transform(function ($student) {
            $totalBill = (float) ($student->billings_sum_amount ?? 0);
            $totalPaid = (float) ($student->payment_details_sum_amount ?? 0);
            $remaining = max(0, $totalBill - $totalPaid);
            
            $status = 'belum_bayar';
            if ($totalBill == 0 || $totalPaid >= $totalBill) {
                $status = 'lunas';
            } else if ($totalPaid > 0) {
                $status = 'nyicil';
            }

            $student->payment_status = $status;
            $student->total_bill = $totalBill;
            $student->total_paid = $totalPaid;
            $student->remaining = $remaining;

            return $student;
        });

        return Inertia::render('Billings/Monitoring', [
            'students' => $students,
            'stats' => $stats,
            'classrooms' => Classroom::with('major')->get(),
            'filters' => $request->only(['search', 'classroom_id', 'per_page', 'status'])
        ]);
    }

    public function printMassal(Request $request)
    {
        $academicYearId = session('academic_year_id');
        
        $query = Student::with([
            'classroom', 
            'classroom.major',
            'billings' => function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId)
                  ->with(['category', 'academicYear'])
                  ->withSum('paymentDetails', 'amount');
            },
            'payments' => function($q) use ($academicYearId) {
                $q->whereHas('paymentDetails.billing', function($sub) use ($academicYearId) {
                    $sub->where('academic_year_id', $academicYearId);
                })->with(['paymentDetails.billing.category', 'user'])->latest('id');
            }
        ])
            ->withSum(['billings' => function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            }], 'amount')
            ->withSum(['paymentDetails' => function($q) use ($academicYearId) {
                $q->where('billings.academic_year_id', $academicYearId);
            }], 'amount');

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('nisn', 'ilike', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $billingSumSql = '(SELECT COALESCE(SUM(amount), 0) FROM billings WHERE billings.student_id = students.id AND billings.academic_year_id = ?)';
            $paymentSumSql = '(SELECT COALESCE(SUM(pd.amount), 0) FROM payment_details pd INNER JOIN billings b ON pd.billing_id = b.id WHERE b.student_id = students.id AND b.academic_year_id = ?)';
            
            if ($status === 'lunas') {
                $query->whereRaw("{$billingSumSql} = 0 OR {$paymentSumSql} >= {$billingSumSql}", [$academicYearId, $academicYearId, $academicYearId]);
            } elseif ($status === 'belum_lunas') { // nyicil
                $query->whereRaw("{$paymentSumSql} > 0 AND {$paymentSumSql} < {$billingSumSql}", [$academicYearId, $academicYearId, $academicYearId]);
            } elseif ($status === 'belum_bayar') {
                $query->whereRaw("{$billingSumSql} > 0 AND {$paymentSumSql} = 0", [$academicYearId, $academicYearId]);
            }
        }

        $students = $query->get();

        $students->transform(function ($student) {
            $totalBill = (float) ($student->billings_sum_amount ?? 0);
            $totalPaid = (float) ($student->payment_details_sum_amount ?? 0);
            $remaining = max(0, $totalBill - $totalPaid);
            
            $status = 'belum_bayar';
            if ($totalBill == 0 || $totalPaid >= $totalBill) {
                $status = 'lunas';
            } else if ($totalPaid > 0) {
                $status = 'nyicil';
            }

            $student->payment_status = $status;
            $student->total_bill = $totalBill;
            $student->total_paid = $totalPaid;
            $student->remaining = $remaining;

            return $student;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.mass_billing_receipt', compact('students'))
            ->setPaper('a4', 'portrait');
        
        return $pdf->stream("cetak_massal_slip_tagihan.pdf");
    }
}
