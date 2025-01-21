<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
       'voucher_no','invoice_no', 'pcs', 'weight', 'destination', 'rate', 'duty', 'amount',
    ];
    
}
