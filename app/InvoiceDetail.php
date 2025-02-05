<?php 

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;
    protected $fillable = ['voucher_id', 'pcs', 'weight', 'rate', 'amt_clring', 'duty', 'total'];

    public function voucher()
    {
        return $this->belongsTo(DeliveryVoucher::class, 'voucher_id');
    }
}
