<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\CashLedger;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        CashLedger::create([
            'date' => $payment->date,
            'type' => 'debit',
            'amount' => $payment->total_amount,
            'description' => "Penerimaan Pembayaran (Ref: {$payment->invoice_number})",
            'reference_type' => Payment::class,
            'reference_id' => $payment->id,
        ]);
    }

    public function deleted(Payment $payment): void
    {
        CashLedger::where('reference_type', Payment::class)
                  ->where('reference_id', $payment->id)
                  ->delete();
    }
}
