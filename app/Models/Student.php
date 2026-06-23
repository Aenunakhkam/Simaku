<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['nisn', 'nis', 'name', 'classroom_id', 'status'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function paymentDetails()
    {
        return $this->hasManyThrough(PaymentDetail::class, Billing::class);
    }
}
