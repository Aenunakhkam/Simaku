<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Billing;
use App\Models\Payment;
use App\Models\PaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['classroom.major'])->where('status', 'active');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('nisn', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->classroom_id) {
            $query->where('classroom_id', $request->classroom_id);
        }

        $students = $query->paginate($request->per_page ?? 10)->withQueryString();
        $classrooms = \App\Models\Classroom::with('major')->get();

        return Inertia::render('Payments/Index', [
            'students' => $students,
            'classrooms' => $classrooms,
            'filters' => $request->only(['search', 'classroom_id', 'per_page']),
        ]);
    }

    public function process($studentId)
    {
        $student = Student::with('classroom.major')->findOrFail($studentId);
        
        $academicYearId = session('academic_year_id');

        $billings = Billing::with(['category', 'academicYear'])
            ->withSum('paymentDetails', 'amount') // Load sum of payments for each billing
            ->where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->get()
            ->map(function ($billing) {
                // Ensure paid_amount and remaining_amount are updated based on withSum
                $billing->paid_amount = (float) $billing->payment_details_sum_amount;
                $billing->remaining_amount = max(0, (float) $billing->amount - $billing->paid_amount);
                return $billing;
            });

        // Ambil riwayat pembayaran (transaksi)
        $payments = Payment::with(['paymentDetails.billing.category', 'paymentDetails.billing.academicYear', 'user'])
            ->whereHas('paymentDetails.billing', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            })
            ->where('student_id', $studentId)
            ->latest()
            ->get();

        return Inertia::render('Payments/Process', [
            'student' => $student,
            'billings' => $billings,
            'payments' => $payments,
        ]);
    }

    public function store(Request $request, $studentId)
    {
        $validated = $request->validate([
            'billings' => 'required|array',
            'billings.*.id' => 'required|exists:billings,id',
            'billings.*.pay_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalPayment = 0;
            $invoiceNumber = 'INV-' . date('YmdHis') . '-' . rand(100, 999);

            $payment = Payment::create([
                'invoice_number' => $invoiceNumber,
                'student_id' => $studentId,
                'user_id' => auth()->id(),
                'date' => now()->toDateString(),
                'total_amount' => 0, // Will update later
            ]);

            foreach ($validated['billings'] as $item) {
                $billing = Billing::findOrFail($item['id']);
                $payAmount = (float) $item['pay_amount'];

                // Ensure they don't overpay
                if ($payAmount > $billing->remaining_amount) {
                    $payAmount = $billing->remaining_amount;
                }

                if ($payAmount > 0) {
                    PaymentDetail::create([
                        'payment_id' => $payment->id,
                        'billing_id' => $billing->id,
                        'amount' => $payAmount,
                    ]);

                    $totalPayment += $payAmount;

                    // Update billing status
                    if ($billing->paid_amount + $payAmount >= $billing->amount) {
                        $billing->update(['is_paid' => true]);
                    }
                }
            }

            $payment->update(['total_amount' => $totalPayment]);

            DB::commit();

            return redirect()->back()->with('message', 'Pembayaran berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function destroy(Payment $payment)
    {
        DB::beginTransaction();
        try {
            // Ambil detail pembayaran
            $details = $payment->paymentDetails()->with('billing')->get();

            foreach ($details as $detail) {
                $billing = $detail->billing;

                // Hitung total cicilan selain transaksi yang akan dihapus ini
                $otherPaymentsTotal = PaymentDetail::where('billing_id', $billing->id)
                    ->where('id', '!=', $detail->id)
                    ->sum('amount');

                // Update is_paid ke false jika totalnya kurang dari tagihan
                if ($otherPaymentsTotal < $billing->amount) {
                    $billing->update(['is_paid' => false]);
                }
            }

            // Hapus detail (jika belum cascade, hapus manual)
            $payment->paymentDetails()->delete();
            
            // Hapus payment utama
            $payment->delete();

            DB::commit();
            return redirect()->back()->with('message', 'Transaksi berhasil dibatalkan. Saldo tagihan telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    public function printInvoicePdf(Payment $payment)
    {
        $payment->load(['student.classroom.major', 'paymentDetails.billing.category', 'paymentDetails.billing.academicYear', 'user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('payment'))
            ->setPaper('a5', 'landscape');

        return $pdf->stream('Kuitansi_' . $payment->invoice_number . '.pdf');
    }
}
