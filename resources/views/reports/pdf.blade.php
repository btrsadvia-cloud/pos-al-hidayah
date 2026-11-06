<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Toko Al-Hidayah</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .summary-box {
            margin-top: 15px;
            float: right;
            width: 250px;
        }
        .summary-table td {
            border: none;
            padding: 3px 0;
        }
        .footer {
            margin-top: 40px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">TOKO AL-HIDAYAH</div>
        <div class="subtitle">Busana Muslimah & Perlengkapan Ibadah</div>
        <div style="font-size: 12px; font-weight: bold; margin-top: 5px;">LAPORAN PENJUALAN</div>
        <div class="subtitle">Periode: {{ date('d/m/Y', strtotime($dateFrom)) }} s/d {{ date('d/m/Y', strtotime($dateTo)) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20px;">#</th>
                <th>No. Faktur</th>
                <th>Tanggal & Waktu</th>
                <th>Kasir</th>
                <th>Pelanggan</th>
                <th>Metode</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Diskon</th>
                <th class="text-right">Total Bersih</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $idx => $s)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $s->invoice_number }}</td>
                    <td>{{ $s->sold_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->user->name ?? '-' }}</td>
                    <td>{{ $s->customer->name ?? 'Umum' }}</td>
                    <td class="text-center">{{ strtoupper($s->payment_method) }}</td>
                    <td class="text-right">{{ number_format($s->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($s->discount, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($s->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada transaksi pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td><strong>Total Transaksi:</strong></td>
                <td class="text-right">{{ $sales->count() }} transaksi</td>
            </tr>
            <tr>
                <td><strong>Total Diskon:</strong></td>
                <td class="text-right">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 1px solid #333;">
                <td style="font-size: 12px;"><strong>TOTAL OMZET:</strong></td>
                <td class="text-right font-bold" style="font-size: 12px; color: #0d9488;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak otomatis oleh Sistem POS Toko Al-Hidayah pada {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>

