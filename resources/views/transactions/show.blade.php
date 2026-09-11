@extends('layouts.app')
@section('title', 'Detail Transaksi ' . $transaction->invoice_number)
@section('page-title', 'Detail Transaksi')
@section('page-subtitle', $transaction->invoice_number)

@section('topbar-actions')
    <a href="{{ route('transactions.receipt', $transaction) }}" target="_blank" class="btn btn-primary btn-sm">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Cetak Struk
    </a>
    @if($transaction->status === 'completed')
        <form action="{{ route('transactions.void', $transaction) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini? Stok produk akan dikembalikan.')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-danger btn-sm">Batalkan Transaksi</button>
        </form>
    @endif
    <a href="{{ route('transactions.index') }}" class="btn btn-ghost btn-sm">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
@push('styles')
<style>
    .payment-summary-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .payment-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid var(--border-subtle);
        font-size: 13px;
    }

    .payment-row.no-border {
        border-bottom: none;
    }

    .payment-label {
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 500;
    }

    .payment-val {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 13px;
        text-align: right;
    }

    .payment-total-box {
        margin: 12px 0;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(15, 23, 42, 0.5));
        border: 1px solid rgba(16, 185, 129, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .payment-total-box {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(240, 253, 244, 0.9));
        border-color: rgba(16, 185, 129, 0.28);
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);
    }

    .payment-total-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: var(--text-muted);
    }

    .payment-total-caption {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .payment-total-value {
        font-size: 22px;
        font-weight: 900;
        color: var(--success);
        letter-spacing: -0.5px;
    }
</style>
@endpush

@section('content')
<div class="grid-2 mb-6">
    {{-- INFO TRANSAKSI --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Informasi Faktur
            </div>
            <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : 'danger' }}">
                {{ $transaction->status === 'completed' ? 'Selesai' : 'Dibatalkan (Void)' }}
            </span>
        </div>
        <div class="card-body">
            <table style="width:100%;">
                <tr>
                    <td style="padding:9px 0; color:var(--text-muted); font-size:13px; width:140px;">No. Invoice</td>
                    <td style="padding:9px 0; font-weight:800; color:var(--primary-light); font-size:14px;">{{ $transaction->invoice_number }}</td>
                </tr>
                <tr>
                    <td style="padding:9px 0; color:var(--text-muted); font-size:13px;">Waktu Transaksi</td>
                    <td style="padding:9px 0; font-size:13px;">{{ $transaction->created_at->format('d F Y, H:i:s') }}</td>
                </tr>
                <tr>
                    <td style="padding:9px 0; color:var(--text-muted); font-size:13px;">Kasir Bertugas</td>
                    <td style="padding:9px 0; font-size:13px; font-weight:600;">{{ $transaction->user->name }}</td>
                </tr>
                <tr>
                    <td style="padding:9px 0; color:var(--text-muted); font-size:13px;">Nama Pelanggan</td>
                    <td style="padding:9px 0; font-size:13px;">
                        {{ $transaction->customer?->name ?? 'Pelanggan Umum' }}
                        @if($transaction->customer?->category)
                            <span class="badge" style="margin-left:6px; font-size:10px; background:rgba(99, 102, 241, 0.15); color:#818cf8; border:1px solid rgba(99, 102, 241, 0.3);">{{ $transaction->customer->category }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding:9px 0; color:var(--text-muted); font-size:13px; {{ !$transaction->notes ? 'border-bottom:none;' : '' }}">Metode Pembayaran</td>
                    <td style="padding:9px 0; {{ !$transaction->notes ? 'border-bottom:none;' : '' }}">
                        <span class="badge badge-purple" style="text-transform:uppercase; font-size:11px;">{{ $transaction->payment_method }}</span>
                    </td>
                </tr>
                @if($transaction->notes)
                <tr>
                    <td style="padding:9px 0; color:var(--text-muted); font-size:13px; border-bottom:none;">Catatan Khusus</td>
                    <td style="padding:9px 0; font-size:13px; color:var(--text-secondary); border-bottom:none;">{{ $transaction->notes }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- RINGKASAN PEMBAYARAN --}}
    <div class="card payment-summary-card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Rincian Pembayaran
            </div>
        </div>
        <div class="card-body">
            <div class="payment-row">
                <span class="payment-label">Subtotal Nilai Barang</span>
                <span class="payment-val">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>

            @if($transaction->discount_amount > 0)
            <div class="payment-row">
                <span class="payment-label">Potongan Diskon</span>
                <span class="payment-val" style="color:var(--danger); font-weight:700;">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif

            @if($transaction->tax_amount > 0)
            <div class="payment-row">
                <span class="payment-label">Pajak Pertambahan Nilai (PPN)</span>
                <span class="payment-val">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
            @endif

            {{-- HIGHLIGHT TOTAL AKHIR --}}
            <div class="payment-total-box">
                <div>
                    <div class="payment-total-label">TOTAL AKHIR</div>
                    <div class="payment-total-caption">Total kewajiban tagihan</div>
                </div>
                <div class="payment-total-value">
                    Rp {{ number_format($transaction->total, 0, ',', '.') }}
                </div>
            </div>

            <div class="payment-row">
                <span class="payment-label">Jumlah Uang Diterima</span>
                <span class="payment-val" style="font-weight:700;">Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
            </div>

            <div class="payment-row no-border">
                <span class="payment-label">Uang Kembalian</span>
                <span class="payment-val" style="font-weight:700; color:{{ $transaction->change_amount > 0 ? 'var(--primary-light)' : 'var(--text-primary)' }};">
                    Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}
                    @if($transaction->change_amount == 0)
                        <span class="badge badge-success" style="font-size:10px; padding:2px 8px; margin-left:6px; font-weight:700;">Pas</span>
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

{{-- TABEL ITEM --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span>Daftar Item Belanja</span>
            <span class="badge badge-purple" style="margin-left:8px;">{{ $transaction->items->count() }} jenis barang</span>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th class="text-right">Harga Satuan</th>
                    <th style="text-align:center;">Kuantitas</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $i => $item)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td style="font-weight:700; color:var(--text-primary);">
                        {{ $item->product_name }}
                    </td>
                    <td>
                        @if($item->product_sku)
                            <code class="font-mono" style="font-size:12px; color:var(--primary-light); background:rgba(5,150,105,0.08); padding:2px 6px; border-radius:4px;">{{ $item->product_sku }}</code>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td style="text-align:center; font-weight:700;">{{ $item->quantity }}</td>
                    <td class="text-right" style="font-weight:800; color:var(--success);">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right; color:var(--text-muted); padding:12px 18px; font-size:13px; font-weight:700;">Total Belanja:</td>
                    <td style="text-align:center; font-weight:800; color:var(--primary-light); padding:12px 18px; font-size:14px;">{{ $transaction->items->sum('quantity') }} item</td>
                    <td class="text-right" style="font-weight:900; font-size:15px; color:var(--success); padding:12px 18px;">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
