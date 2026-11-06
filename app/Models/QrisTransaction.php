<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrisTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'sale_id',
        'invoice_number',
        'amount',
        'total_amount',
        'amount_uniq',
        'admin_fee',
        'credit_amount',
        'fee_by',
        'qris_method',
        'qr_url',
        'qris_image',
        'payment_url',
        'status',
        'is_test',
        'paid_at',
        'expired_at',
        'raw_response',
        'created_by',
    ];

    protected $casts = [
        'amount'        => 'integer',
        'total_amount'  => 'integer',
        'amount_uniq'   => 'integer',
        'admin_fee'     => 'integer',
        'credit_amount' => 'integer',
        'is_test'       => 'boolean',
        'paid_at'       => 'datetime',
        'expired_at'    => 'datetime',
        'raw_response'  => 'array',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}

