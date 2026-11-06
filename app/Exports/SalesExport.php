<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom = null, $dateTo = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        $query = Sale::with(['user', 'customer'])->where('status', 'completed');

        if ($this->dateFrom) {
            $query->whereDate('sold_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('sold_at', '<=', $this->dateTo);
        }

        return $query->orderBy('sold_at')->get();
    }

    public function headings(): array
    {
        return [
            'No. Faktur',
            'Tanggal & Waktu',
            'Kasir',
            'Pelanggan',
            'Metode Pembayaran',
            'Subtotal (Rp)',
            'Diskon (Rp)',
            'Total (Rp)',
            'Status',
        ];
    }

    /**
     * @param Sale $sale
     */
    public function map($sale): array
    {
        return [
            $sale->invoice_number,
            $sale->sold_at ? $sale->sold_at->format('d/m/Y H:i') : '-',
            $sale->user->name ?? '-',
            $sale->customer->name ?? 'Umum',
            strtoupper($sale->payment_method),
            $sale->subtotal,
            $sale->discount,
            $sale->total,
            $sale->status === 'completed' ? 'Selesai' : 'Dibatalkan',
        ];
    }
}
