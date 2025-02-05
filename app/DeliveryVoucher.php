<?php 
namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryVoucher extends Model
{
    use HasFactory;
    protected $fillable = ['voucher_id', 'ship_no', 'party_id', 'date','remarks'];

    public function invoices()
    {
        return $this->hasMany(InvoiceDetail::class, 'voucher_id');
    }
}
