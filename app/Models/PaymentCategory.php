<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'default_amount'];

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }
}
