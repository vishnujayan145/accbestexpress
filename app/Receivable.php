<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Define the relationship with the IncomeExpenseHead model
    public function incomeExpenseHead()
    {
        return $this->belongsTo(IncomeExpenseHead::class);
    }

    // Define the relationship with the BankCash model
    public function bankCash()
    {
        return $this->belongsTo(BankCash::class);
    }
    use HasFactory;
    protected $fillable = [
        'voucher_no', 'date', 'branch_name', 'head_of_account_name','total_amount'
        // Add other fields that you need to store
    ];
   
}
