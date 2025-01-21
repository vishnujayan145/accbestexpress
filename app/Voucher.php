<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'voucher_id', 'date', 'branch', 'bank_name', 'head_of_account', 'description','total_amount'
        // Add other fields that you need to store
    ];
   
}
