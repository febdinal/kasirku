<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            padding: 20px;
            min-height: 100vh;
            /* Pastikan teks tajam di layar */
            -webkit-font-smoothing: antialiased;
        }

        .receipt {
            background: white;
            width: 300px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            position: relative;
            /* Base: semua teks tebal & hitam agar cetak jelas */
            font-weight: 600;
            color: #111;
        }

        .receipt::before, .receipt::after {
            content: '';
            position: absolute;
            left: 0; right: 0;
            height: 12px;
        }

        .receipt::before {
            top: -12px;
            background: repeating-linear-gradient(90deg, white 0, white 12px, transparent 12px, transparent 24px);
        }

        .receipt::after {
            bottom: -12px;
            background: repeating-linear-gradient(90deg, white 0, white 12px, transparent 12px, transparent 24px);
        }

        .store-name {
            text-align: center;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 4px;
            color: #000;
            margin-bottom: 2px;
        }

        .store-tagline {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: #222;
            margin-bottom: 4px;
        }

        .store-info {
            text-align: center;
            font-size: 10px;
            font-weight: 600;
            color: #333;
            line-height: 1.6;
        }

        .divider {
            border: none;
            border-top: 1px dashed #999;
            margin: 12px 0;
        }

        .divider-solid {
            border: none;
            border-top: 1.5px solid #888;
            margin: 12px 0;
        }

        .invoice-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 600;
            color: #111;
            margin-bottom: 4px;
        }

        .items-table { width: 100%; }

        .item-row {
            margin-bottom: 8px;
        }

        .item-name {
            font-size: 12px;
            font-weight: 800;
            color: #000;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 600;
            color: #222;
            margin-top: 2px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .summary-label { color: #222; font-weight: 600; }
        .summary-value { font-weight: 700; color: #111; }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            font-weight: 900;
            color: #000;
            margin: 4px 0;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
            color: #111;
        }

        .change-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 800;
            color: #000;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 800;
        }

        .footer-text {
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            color: #333;
            line-height: 1.8;
            margin-top: 8px;
        }

        .print-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 24px;
        }

        .print-btn {
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
        }

        .print-btn-primary {
            background: #6366f1;
            color: white;
        }

        .print-btn-ghost {
            background: #f1f5f9;
            color: #334155;
        }

        @media print {
            body { background: white; padding: 0; display: block; }
            .receipt {
                box-shadow: none;
                width: 100%;
                border-radius: 0;
                /* Paksa semua teks hitam saat print */
                color: #000 !important;
            }
            .receipt::before, .receipt::after { display: none; }
            .print-actions { display: none; }
            /* Override semua warna teks menjadi hitam solid saat print */
            .store-name, .store-tagline, .store-info,
            .invoice-row, .invoice-row span,
            .item-name, .item-detail, .item-detail span,
            .summary-row, .summary-label, .summary-value,
            .total-row, .payment-row, .payment-row span,
            .change-row, .footer-text {
                color: #000 !important;
            }
        }
    </style>
</head>
<body>
<div>
    <div class="receipt">
        {{-- STORE HEADER --}}
        <div class="store-name">{{ \App\Models\Setting::get('store_name', 'VENTRA') }}</div>
        <div class="store-tagline">{{ \App\Models\Setting::get('store_tagline', '') }}</div>
        <div class="store-info">
            {{ \App\Models\Setting::get('store_address', '') }}<br>
            @if(\App\Models\Setting::get('store_phone'))
                Telp: {{ \App\Models\Setting::get('store_phone') }}<br>
            @endif
        </div>

        <hr class="divider">

        {{-- INVOICE INFO --}}
        <div class="invoice-row">
            <span>No. Invoice</span>
            <span style="font-weight:700;">{{ $transaction->invoice_number }}</span>
        </div>
        <div class="invoice-row">
            <span>Tanggal</span>
            <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="invoice-row">
            <span>Kasir</span>
            <span>{{ $transaction->user->name }}</span>
        </div>
        @if($transaction->customer)
        <div class="invoice-row">
            <span>Pelanggan</span>
            <span>{{ $transaction->customer->name }}</span>
        </div>
        @endif
        @if($transaction->status === 'voided')
        <div class="invoice-row" style="margin-top:6px;">
            <span></span>
            <span class="badge-status" style="background:#fee2e2; color:#991b1b;">⚠ DIBATALKAN</span>
        </div>
        @endif

        <hr class="divider">

        {{-- ITEMS --}}
        @foreach($transaction->items as $item)
        <div class="item-row">
            <div class="item-name">{{ $item->product_name }}</div>
            <div class="item-detail">
                <span>{{ $item->quantity }}x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                <span style="font-weight:700;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach

        <hr class="divider-solid">

        {{-- SUMMARY --}}
        <div class="summary-row">
            <span class="summary-label">Subtotal</span>
            <span class="summary-value">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
        </div>

        @if($transaction->discount_amount > 0)
        <div class="summary-row">
            <span class="summary-label">Diskon</span>
            <span class="summary-value" style="color:#dc2626;">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif

        @if($transaction->tax_amount > 0)
        <div class="summary-row">
            <span class="summary-label">PPN</span>
            <span class="summary-value">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
        </div>
        @endif

        <hr class="divider">

        <div class="total-row">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
        </div>

        <hr class="divider">

        <div class="payment-row">
            <span>Metode Bayar</span>
            <span style="font-weight:700; text-transform:uppercase;">{{ $transaction->payment_method }}</span>
        </div>
        <div class="payment-row">
            <span>Jumlah Bayar</span>
            <span>Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
        </div>
        <div class="change-row">
            <span>Kembalian</span>
            <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>

        <hr class="divider">

        {{-- FOOTER --}}
        <div class="footer-text">
            {{ \App\Models\Setting::get('receipt_footer', 'Terima kasih telah berbelanja!') }}<br>
            <span style="font-size:10px; color:#bbb;">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>

    <div class="print-actions">
        <button class="print-btn print-btn-primary" onclick="window.print()">🖨️ Cetak Struk</button>
        <button class="print-btn print-btn-ghost" onclick="window.close()">✕ Tutup</button>
    </div>
</div>
<script>
    // Auto print when opened from POS
    window.addEventListener('load', () => {
        setTimeout(() => window.print(), 500);
    });
</script>
</body>
</html>
