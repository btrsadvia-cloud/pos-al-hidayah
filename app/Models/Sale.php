<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'user_id',
        'shift_id',
        'subtotal',
        'discount',
        'total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'payment_reference',
        'status',
        'note',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'discount' => 'integer',
            'total' => 'integer',
            'paid_amount' => 'integer',
            'change_amount' => 'integer',
            'sold_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Generate consecutive invoice number with row lock within transaction.
     * Format: INV-YYYYMMDD-NNNN
     */
    public static function generateInvoiceNumber(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $dateStr = $date->format('Ymd');
        $prefix = "INV-{$dateStr}-";

        // Query with lockForUpdate to ensure concurrency safety
        $lastSale = static::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastSale) {
            $lastSeq = (int) substr($lastSale->invoice_number, -4);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return sprintf('%s%04d', $prefix, $nextSeq);
    }
}

