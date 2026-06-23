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
        
        $query = Student::with(['classroom', 'classroom.major'])
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

        // Filter by search name
        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('nisn', 'ilike', '%' . $request->search . '%');
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
            'filters' => $request->only(['search', 'classroom_id', 'per_page'])
        ]);
    }
}
