<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'payment_category_id', 'academic_year_id', 'month', 'amount', 'is_paid'];

    public function student() { return $this->belongsTo(Student::class); }
    public function category() { return $this->belongsTo(PaymentCategory::class, 'payment_category_id'); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    
    public function paymentDetails() { return $this->hasMany(PaymentDetail::class); }

    protected $appends = ['paid_amount', 'remaining_amount'];

    public function getPaidAmountAttribute()
    {
        if (array_key_exists('payment_details_sum_amount', $this->attributes)) {
            return (float) ($this->attributes['payment_details_sum_amount'] ?? 0);
        }
        // Fallback (N+1 warning, but safe)
        return (float) $this->paymentDetails()->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, (float) $this->amount - $this->paid_amount);
    }
}
