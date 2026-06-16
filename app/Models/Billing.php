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
}
