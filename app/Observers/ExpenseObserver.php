<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\CashLedger;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        CashLedger::create([
            'date' => $expense->date,
            'type' => 'credit',
            'amount' => $expense->amount,
            'description' => "Pengeluaran: {$expense->note} (Ref: {$expense->voucher_number})",
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
        ]);
    }

    public function deleted(Expense $expense): void
    {
        CashLedger::where('reference_type', Expense::class)
                  ->where('reference_id', $expense->id)
                  ->delete();
    }
}
