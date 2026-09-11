<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #1e293b;
        }
        .header-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
        }
        .header-sub {
            font-size: 10pt;
            color: #64748b;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th {
            background-color: #3b82f6;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: 1px solid #2563eb;
            padding: 8px;
            height: 30px;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .bg-gray { background-color: #f1f5f9; }
        .bg-summary { background-color: #e0e7ff; font-weight: bold; }
        .badge-completed { color: #059669; font-weight: bold; }
        .badge-voided { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>

    {{-- KOP LAPORAN --}}
    <table>
        <tr>
            <td colspan="11" class="header-title">{{ strtoupper($storeName) }}</td>
        </tr>
        <tr>
            <td colspan="11" class="header-sub">{{ $storeAddress }} {{ $storePhone ? '· Telp: ' . $storePhone : '' }}</td>
        </tr>
        <tr>
            <td colspan="11" style="font-size: 13pt; font-weight: bold; padding-top: 10px;">
                LAPORAN PENJUALAN & TRANSAKSI
            </td>
        </tr>
        <tr>
            <td colspan="11" class="header-sub">
                Periode: <strong>{{ $periodLabel }}</strong> | Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i:s') }}
            </td>
        </tr>
    </table>

    {{-- RINGKASAN METRIK --}}
    <table>
        <tr style="background-color: #1e293b; color: #ffffff;">
            <th colspan="4" style="background-color: #1e293b; text-align: left; font-size: 12pt;">
                RINGKASAN EKSEKUTIF
            </th>
        </tr>
        <tr>
            <td style="width: 250px; font-weight: bold;" class="bg-gray">Total Transaksi Selesai</td>
            <td style="width: 150px; font-weight: bold;" class="text-right">{{ number_format($totalTransactions, 0, ',', '.') }} Transaksi</td>
            <td style="width: 200px; font-weight: bold;" class="bg-gray">Total Nilai Kotor (Subtotal)</td>
            <td style="width: 180px; font-weight: bold;" class="text-right">{{ $currency }} {{ number_format($totalSubtotal, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;" class="bg-gray">Total Potongan Diskon</td>
            <td style="color: #dc2626; font-weight: bold;" class="text-right">- {{ $currency }} {{ number_format($totalDiscount, 2, ',', '.') }}</td>
            <td style="font-weight: bold;" class="bg-gray">Total PPN Terkumpul</td>
            <td style="font-weight: bold;" class="text-right">{{ $currency }} {{ number_format($totalTax, 2, ',', '.') }}</td>
        </tr>
        <tr class="bg-summary">
            <td colspan="2" style="font-size: 12pt; text-align: right;">TOTAL PENDAPATAN BERSIH:</td>
            <td colspan="2" style="font-size: 13pt; color: #1e40af; text-align: right;">
                {{ $currency }} {{ number_format($totalRevenue, 2, ',', '.') }}
            </td>
        </tr>
    </table>

    {{-- BREAKDOWN METODE PEMBAYARAN --}}
    <table>
        <tr>
            <th style="background-color: #475569; width: 50px;">No</th>
            <th style="background-color: #475569; text-align: left;">Metode Pembayaran</th>
            <th style="background-color: #475569; width: 150px;">Jumlah Transaksi</th>
            <th style="background-color: #475569; width: 220px; text-align: right;">Total Nominal</th>
        </tr>
        @php $noPm = 1; @endphp
        @foreach($paymentBreakdown as $pm)
        <tr>
            <td class="text-center">{{ $noPm++ }}</td>
            <td class="font-bold">{{ $pm['method'] }}</td>
            <td class="text-center">{{ $pm['count'] }}</td>
            <td class="text-right font-bold">{{ $currency }} {{ number_format($pm['total'], 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <br>

    {{-- RINCIAN SEMUA TRANSAKSI --}}
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 140px;">No. Invoice</th>
                <th style="width: 130px;">Waktu Transaksi</th>
                <th style="width: 180px;">Pelanggan</th>
                <th style="width: 110px;">Kategori Pelanggan</th>
                <th style="width: 120px;">Kasir</th>
                <th style="width: 110px;">Metode Bayar</th>
                <th style="width: 120px; text-align: right;">Subtotal</th>
                <th style="width: 100px; text-align: right;">Diskon</th>
                <th style="width: 100px; text-align: right;">PPN</th>
                <th style="width: 130px; text-align: right;">Total Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $idx => $t)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="font-bold" style="mso-number-format:'\@';">{{ $t->invoice_number }}</td>
                <td class="text-center">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $t->customer?->name ?? 'Pelanggan Umum' }}</td>
                <td class="text-center">{{ $t->customer?->category ?? 'Umum' }}</td>
                <td>{{ $t->user?->name ?? '-' }}</td>
                <td class="text-center font-bold">{{ strtoupper($t->payment_method) }}</td>
                <td class="text-right">{{ number_format($t->subtotal, 2, ',', '.') }}</td>
                <td class="text-right" style="{{ $t->discount_amount > 0 ? 'color:#dc2626;' : '' }}">
                    {{ number_format($t->discount_amount, 2, ',', '.') }}
                </td>
                <td class="text-right">{{ number_format($t->tax_amount, 2, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($t->total, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center" style="padding: 20px; color: #94a3b8;">
                    Tidak ada transaksi pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-summary">
                <td colspan="7" class="text-right font-bold">TOTAL KESELURUHAN:</td>
                <td class="text-right font-bold">{{ number_format($totalSubtotal, 2, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($totalDiscount, 2, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($totalTax, 2, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($totalRevenue, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <br>

    {{-- RINGKASAN PRODUK TERJUAL --}}
    <table>
        <thead>
            <tr style="background-color: #0d9488;">
                <th colspan="4" style="background-color: #0d9488; text-align: left; font-size: 12pt;">
                    RINGKASAN PENJUALAN PRODUK
                </th>
            </tr>
            <tr>
                <th style="width: 40px; background-color: #14b8a6;">No</th>
                <th style="background-color: #14b8a6; text-align: left;">Nama Produk</th>
                <th style="width: 120px; background-color: #14b8a6; text-align: center;">Qty Terjual</th>
                <th style="width: 180px; background-color: #14b8a6; text-align: right;">Total Nilai Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @php $noProd = 1; @endphp
            @forelse($productsSold as $prod)
            <tr>
                <td class="text-center">{{ $noProd++ }}</td>
                <td>{{ $prod['name'] }}</td>
                <td class="text-center font-bold">{{ number_format($prod['qty'], 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ $currency }} {{ number_format($prod['total'], 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="color: #94a3b8;">Tidak ada produk terjual.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
