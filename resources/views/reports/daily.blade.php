@extends('layouts.app')
@section('title', 'Laporan Harian')
@section('page-title', 'Laporan Penjualan Harian')
@section('page-subtitle', 'Rekap transaksi dan performa penjualan per tanggal')

@section('topbar-actions')
    <div style="display:flex; gap:8px;">
        <a href="{{ route('reports.export-excel', ['period_type' => 'daily', 'date' => $date]) }}" class="btn btn-success btn-sm">
            📥 Export Excel Hari Ini
        </a>
        <button type="button" class="btn btn-ghost btn-sm" onclick="openExportModal('daily', '{{ $date }}')">
            📊 Opsi Periode Lain
        </button>
    </div>
@endsection

@section('content')
{{-- FILTER TANGGAL --}}
<div class="filter-card">
    <form method="GET" class="filter-form">
        <div class="filter-group">
            <label class="filter-label">Pilih Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ $date }}" style="width:200px;">
        </div>
        <div class="filter-actions" style="margin-bottom:1px;">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <a href="{{ route('reports.daily') }}" class="btn btn-ghost">Hari Ini</a>
            <a href="{{ route('reports.export-excel', ['period_type' => 'daily', 'date' => $date]) }}" class="btn btn-success" style="margin-left:auto;">
                📥 Export Excel (.xls)
            </a>
        </div>
    </form>
</div>

{{-- STATS CARDS --}}
<div class="grid-3 mb-6">
    <div class="stat-card green">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="stat-icon green">💰</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">{{ $totalTransactions }} <span style="font-size:14px; font-weight:500; color:var(--text-muted);">transaksi</span></div>
        <div class="stat-icon purple">🛒</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-label">Item Produk Terjual</div>
        <div class="stat-value">{{ $totalItems }} <span style="font-size:14px; font-weight:500; color:var(--text-muted);">pcs</span></div>
        <div class="stat-icon blue">📦</div>
    </div>
</div>

<div class="grid-2">
    {{-- TOP PRODUK --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>🏆 Produk Terlaris</span>
            </div>
            <span style="font-size:12px; color:var(--text-muted);">Top 5 item</span>
        </div>
        <div class="card-body" style="padding:12px 20px;">
            @forelse($topProducts as $i => $p)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid var(--border-subtle);">
                <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                    <span style="width:28px; height:28px; border-radius:50%; background:{{ $i === 0 ? 'var(--warning)' : 'var(--bg-elevated)' }}; color:{{ $i === 0 ? '#000' : 'var(--text-primary)' }}; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0;">{{ $i+1 }}</span>
                    <div style="min-width:0;">
                        <div style="font-size:13px; font-weight:700; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $p->product_name }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">Terjual: {{ $p->total_qty }} pcs</div>
                    </div>
                </div>
                <div style="text-align:right; flex-shrink:0;">
                    <div style="font-size:13px; font-weight:800; color:var(--success);">Rp {{ number_format($p->total_revenue, 0, ',', '.') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:40px; color:var(--text-muted);">
                Tidak ada data produk terjual pada tanggal ini
            </div>
            @endforelse
        </div>
    </div>

    {{-- DAFTAR TRANSAKSI --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>Daftar Transaksi</span>
            </div>
            <span style="font-size:12px; color:var(--text-muted);">{{ count($transactions) }} invoice</span>
        </div>
        <div style="overflow-y:auto; max-height:420px; padding:0 20px;">
            @forelse($transactions as $trx)
            <div style="padding:14px 0; border-bottom:1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <a href="{{ route('transactions.show', $trx) }}" style="font-size:13px; font-weight:700; color:var(--primary-light); text-decoration:none;">
                        {{ $trx->invoice_number }}
                    </a>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                        {{ $trx->created_at->format('H:i') }} · <span style="text-transform:uppercase;">{{ $trx->payment_method }}</span> · {{ $trx->customer?->name ?? 'Umum' }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:800; color:var(--success); font-size:14px;">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:40px; text-align:center; color:var(--text-muted);">
                Tidak ada transaksi tercatat pada tanggal ini
            </div>
            @endforelse
        </div>
    </div>
</div>

@include('reports._export_modal')
@endsection
