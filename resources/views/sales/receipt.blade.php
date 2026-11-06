<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: #fff;
            width: 58mm;
            margin: 0 auto;
            padding: 10px 6px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .border-b { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        .border-t { border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px; }
        .flex { display: flex; justify-content: space-between; }
        .truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item-row { margin-bottom: 4px; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 12px; background: #0d9488; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">Cetak Struk</button>
        <button onclick="window.close()" style="padding: 6px 12px; background: #ccc; color: #000; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; margin-left: 5px;">Tutup</button>
    </div>

    <div class="text-center border-b">
        <div class="font-bold" style="font-size: 13px;">TOKO AL-HIDAYAH</div>
        <div>Busana Muslimah & Perlengkapan Ibadah</div>
        <div style="font-size: 10px;">Pasar Raya Blok A No. 12, Indonesia</div>
        <div style="font-size: 10px;">Telp/WA: 0812-3456-7890</div>
    </div>

    <div class="border-b" style="font-size: 10px;">
        <div class="flex">
            <span>No: {{ $sale->invoice_number }}</span>
            <span>{{ $sale->sold_at ? $sale->sold_at->format('d/m/y H:i') : '-' }}</span>
        </div>
        <div class="flex">
            <span>Kasir: {{ $sale->user->name ?? 'Kasir' }}</span>
            <span>Pel: {{ $sale->customer->name ?? 'Umum' }}</span>
        </div>
    </div>

    <div class="border-b">
        @foreach($sale->items as $it)
            <div class="item-row">
                <div class="font-bold truncate">{{ $it->product_name }}</div>
                <div class="flex" style="font-size: 10px;">
                    <span>{{ $it->quantity }} {{ $it->unit_name }} x {{ number_format($it->unit_price, 0, ',', '.') }}</span>
                    <span>{{ number_format($it->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($it->item_discount > 0)
                    <div class="text-right" style="font-size: 9px;">Disc: -{{ number_format($it->item_discount, 0, ',', '.') }}</div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="border-b" style="font-size: 11px;">
        <div class="flex">
            <span>Subtotal:</span>
            <span>{{ number_format($sale->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($sale->discount > 0)
            <div class="flex">
                <span>Diskon:</span>
                <span>-{{ number_format($sale->discount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="flex font-bold" style="font-size: 12px; margin-top: 2px;">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
        </div>
        <div class="flex" style="font-size: 10px; margin-top: 3px;">
            <span>Bayar ({{ strtoupper($sale->payment_method) }}):</span>
            <span>{{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
        </div>
        @if($sale->payment_method === 'cash')
            <div class="flex font-bold" style="font-size: 10px;">
                <span>Kembali:</span>
                <span>{{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="text-center" style="font-size: 10px; margin-top: 8px;">
        Terima kasih telah berbelanja di<br>
        <strong>Toko Al-Hidayah</strong><br>
        Barang yang sudah dibeli dapat ditukar maksimal 2 hari dengan membawa nota ini.<br>
        <em>Jazakumullahu Khairan Katsiran</em>
    </div>

</body>
</html>

