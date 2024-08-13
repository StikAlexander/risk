<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class VoucherPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'payment_date',
        'amount',
        'voucher',
        'epayco_ref',
        'epayco_transaction_id',
        'epayco_transaction_date',
        'pdf_path',
        'confirmation_status',
        'confirmed_by',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
