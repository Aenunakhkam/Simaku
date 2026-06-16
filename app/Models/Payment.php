<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_number', 'student_id', 'user_id', 'date', 'total_amount'];

    public function student() { return $this->belongsTo(Student::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function details() { return $this->hasMany(PaymentDetail::class); }
}
