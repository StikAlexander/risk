<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'invoice_number',
        'issue_date',
        'due_date',
        'total_amount',
        'client_id',
        'created_by',
        'status',
        'pending_amount',
        'total_paid',
        'epayco_ref',
        'epayco_status',
        'invoice_pdf',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

            if (!empty($model->issue_date)) {
                $model->due_date = Carbon::parse($model->issue_date)->addDays(30);
            }
        });

        static::saving(function ($model) {
            if (!empty($model->total_amount)) {
                $model->pending_amount = $model->total_amount - ($model->total_paid ?? 0);
            }

            if ($model->total_paid >= $model->total_amount) {
                $model->status = 'Paid';
            } else {
                $model->status = 'Pending';
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voucherPayments()
    {
        return $this->hasMany(VoucherPayment::class);
    }
}