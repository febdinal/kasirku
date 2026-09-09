@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')
@section('page-subtitle', 'Daftar seluruh transaksi kasir yang tercatat')

@section('content')
{{-- FILTER BAR --}}
<div class="filter-card">
    <form method="GET" class="filter-form">
        <div class="filter-group" style="flex:2; min-width:200px;">
            <label class="filter-label">No. Invoice</label>
            <input type="text" name="search" class="form-control" placeholder="Cari invoice..." value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <label class="filter-label">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="filter-group">
            <label class="filter-label">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="filter-group" style="min-width:140px;">
            <label class="filter-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="voided" {{ request('status') === 'voided' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="filter-actions" style="margin-bottom:1px;">
            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
            @if(request()->hasAny(['search','date_from','date_to','status']))
                <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span>Daftar Transaksi</span>
            <span class="badge badge-purple" style="margin-left:8px;">{{ $transactions->total() }} total</span>
        </div>
        <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">
            + Transaksi Kasir Baru
        </a>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Invoice</th>
                    <th>Pelanggan</th>
                    <th>Metode Bayar</th>
                    <th class="text-right">Total Tagihan</th>
                    <th>Kasir</th>
                    <th>Waktu Transaksi</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right; width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $trx)
                <tr>
                    <td class="text-muted">{{ $transactions->firstItem() + $index }}</td>
                    <td>
                        <a href="{{ route('transactions.show', $trx) }}" style="font-weight:700; color:var(--primary-light); text-decoration:none;">
                            {{ $trx->invoice_number }}
                        </a>
                    </td>
                    <td>
                        {{ $trx->customer?->name ?? 'Pelanggan Umum' }}
                    </td>
                    <td>
                        <span class="badge badge-purple" style="text-transform:uppercase; font-size:10px;">{{ $trx->payment_method }}</span>
                    </td>
                    <td class="text-right" style="font-weight:800; color:var(--success); font-size:14px;">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </td>
                    <td style="color:var(--text-secondary);">{{ $trx->user->name }}</td>
                    <td style="font-size:12px; color:var(--text-muted);">
                        {{ $trx->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-{{ $trx->status === 'completed' ? 'success' : 'danger' }}">
                            {{ $trx->status === 'completed' ? 'Selesai' : 'Dibatalkan' }}
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <div class="flex gap-2" style="justify-content:flex-end;">
                            <a href="{{ route('transactions.show', $trx) }}" class="btn btn-ghost btn-sm">Detail</a>
                            <a href="{{ route('transactions.receipt', $trx) }}" target="_blank" class="btn btn-ghost btn-sm" title="Cetak Struk">
                                🖨️
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding:50px; color:var(--text-muted);">
                        <div style="font-size:32px; margin-bottom:8px; opacity:0.6;">🧾</div>
                        <div style="font-weight:600;">Tidak ada data transaksi</div>
                        <div style="font-size:12px; margin-top:4px;">Gunakan tombol POS Kasir untuk membuat transaksi baru</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="pagination-wrapper">
        <div style="font-size:12px; color:var(--text-muted);">
            Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
        </div>
        <div>
            {{ $transactions->links('pagination::simple-bootstrap-5') }}
        </div>
    </div>
    @endif
</div>
@endsection
