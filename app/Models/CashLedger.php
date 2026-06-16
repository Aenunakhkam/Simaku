<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashLedger extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'type', 'amount', 'description', 'reference_type', 'reference_id'];

    public function reference() { return $this->morphTo(); }
}
