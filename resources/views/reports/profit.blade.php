@extends('layouts.app')
@section('title', 'Laporan Laba / Rugi')
@section('page-title', 'Laporan Laba / Rugi')
@section('page-subtitle', 'Analisis margin keuntungan dan harga pokok penjualan (HPP)')

@section('content')
{{-- FILTER BULAN --}}
<div class="filter-card">
    <form method="GET" class="filter-form">
        <div class="filter-group">
            <label class="filter-label">Pilih Periode Bulan</label>
            <input type="month" name="month" class="form-control" value="{{ $month }}" style="width:200px;">
        </div>
        <div class="filter-actions" style="margin-bottom:1px;">
            <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
            <a href="{{ route('reports.profit') }}" class="btn btn-ghost">Bulan Ini</a>
        </div>
    </form>
</div>

{{-- STAT CARDS --}}
<div class="grid-4 mb-6">
    <div class="stat-card green">
        <div class="stat-label">Total Penjualan</div>
        <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="stat-icon green">💰</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-label">Total HPP / Modal</div>
        <div class="stat-value">Rp {{ number_format($totalCogs, 0, ',', '.') }}</div>
        <div class="stat-icon amber">📦</div>
    </div>
    <div class="stat-card {{ $totalProfit >= 0 ? 'green' : 'blue' }}">
        <div class="stat-label">Laba Kotor Bersih</div>
        <div class="stat-value" style="color:{{ $totalProfit >= 0 ? 'var(--success)' : 'var(--danger)' }}">
            Rp {{ number_format($totalProfit, 0, ',', '.') }}
        </div>
        <div class="stat-icon {{ $totalProfit >= 0 ? 'green' : 'blue' }}">{{ $totalProfit >= 0 ? '📈' : '📉' }}</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">Persentase Margin</div>
        <div class="stat-value">{{ number_format($profitMargin, 1) }}%</div>
        <div class="stat-icon purple">🎯</div>
    </div>
</div>

{{-- RINCIAN PER PRODUK --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Rincian Laba & Margin Per Produk</div>
        <span style="font-size:12px; color:var(--text-muted);">{{ count($items) }} varian terjual</span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Nama Produk</th>
                    <th style="text-align:center;">Qty Terjual</th>
                    <th class="text-right">Total Penjualan</th>
                    <th class="text-right">Total HPP (Modal)</th>
                    <th class="text-right">Laba Kotor</th>
                    <th style="text-align:center; width:110px;">Margin Laba</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $i => $item)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td style="font-weight:700; color:var(--text-primary);">
                        {{ $item->product_name }}
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-purple">{{ $item->total_qty }} pcs</span>
                    </td>
                    <td class="text-right" style="font-weight:700;">
                        Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                    </td>
                    <td class="text-right text-muted">
                        Rp {{ number_format($item->total_cogs, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="font-weight:800; color:{{ $item->total_profit >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                        Rp {{ number_format($item->total_profit, 0, ',', '.') }}
                    </td>
                    <td style="text-align:center;">
                        @php $margin = $item->total_revenue > 0 ? ($item->total_profit / $item->total_revenue * 100) : 0 @endphp
                        <span class="badge badge-{{ $margin >= 20 ? 'success' : ($margin >= 10 ? 'warning' : 'danger') }}">
                            {{ number_format($margin, 1) }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:50px; color:var(--text-muted);">
                        <div style="font-size:32px; margin-bottom:8px; opacity:0.6;">📊</div>
                        <div style="font-weight:600;">Tidak ada data transaksi pada periode ini</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($items->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="2" style="font-weight:800; letter-spacing:0.5px;">TOTAL PERIODE INI</td>
                    <td style="text-align:center; font-weight:800;">{{ $items->sum('total_qty') }} pcs</td>
                    <td class="text-right" style="font-weight:800; color:var(--text-primary);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    <td class="text-right text-muted" style="font-weight:700;">Rp {{ number_format($totalCogs, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight:900; font-size:15px; color:{{ $totalProfit >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                        Rp {{ number_format($totalProfit, 0, ',', '.') }}
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-{{ $profitMargin >= 20 ? 'success' : 'warning' }}" style="font-size:12px; font-weight:700;">
                            {{ number_format($profitMargin, 1) }}%
                        </span>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
