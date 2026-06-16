<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $academicYearId = session('academic_year_id');

        // 1. Total Saldo Kas (Global)
        $totalIncome = Payment::sum('total_amount');
        $totalExpense = Expense::sum('amount');
        $totalKas = $totalIncome - $totalExpense;

        // 2. Pemasukan Bulan Ini (Khusus Tahun Ajaran Aktif)
        $incomeThisMonth = Payment::whereHas('paymentDetails.billing', function ($query) use ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        })
        ->whereMonth('date', now()->month)
        ->whereYear('date', now()->year)
        ->sum('total_amount');

        // 3. Pengeluaran Bulan Ini (Global)
        $expenseThisMonth = Expense::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        // 4. Total Tunggakan (Khusus Tahun Ajaran Aktif)
        $totalBillingAmount = Billing::where('academic_year_id', $academicYearId)->sum('amount');
        $totalPaidForBillings = PaymentDetail::whereHas('billing', function ($query) use ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        })->sum('amount');
        $totalTunggakan = max(0, $totalBillingAmount - $totalPaidForBillings);

        // Ambil transaksi terakhir (gabungan income dan expense) - Opsional, untuk sementara kirim kosong atau data terbaru
        $recentPayments = Payment::with(['user', 'student'])->latest()->take(5)->get();

        $year = request('year', now()->year);

        $incomeData = array_fill(1, 12, 0);
        $expenseData = array_fill(1, 12, 0);

        $payments = Payment::whereYear('date', $year)->get(['date', 'total_amount']);
        foreach ($payments as $payment) {
            $month = (int) date('m', strtotime($payment->date));
            $incomeData[$month] += $payment->total_amount;
        }

        $expenses = Expense::whereYear('date', $year)->get(['date', 'amount']);
        foreach ($expenses as $expense) {
            $month = (int) date('m', strtotime($expense->date));
            $expenseData[$month] += $expense->amount;
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_kas' => (float) $totalKas,
                'income_this_month' => (float) $incomeThisMonth,
                'expense_this_month' => (float) $expenseThisMonth,
                'total_tunggakan' => (float) $totalTunggakan,
            ],
            'recent_transactions' => $recentPayments,
            'chartData' => [
                'income' => array_values($incomeData),
                'expense' => array_values($expenseData),
                'selected_year' => (int) $year
            ]
        ]);
    }
}
