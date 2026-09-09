@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan performa dan aktivitas toko ' . \App\Models\Setting::get('store_name', 'VENTRA'))

@section('topbar-actions')
    <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buka Kasir POS
    </a>
@endsection

@push('styles')
<style>
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
    .chart-grid { display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 20px; margin-bottom: 24px; }
    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .chart-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
{{-- STAT CARDS --}}
<div class="stats-grid">
    <div class="stat-card green">
        <div class="stat-label">Pendapatan Hari Ini</div>
        <div class="stat-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
        <div class="stat-icon green">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">Transaksi Hari Ini</div>
        <div class="stat-value">{{ number_format($todayTransactions) }} <span style="font-size:14px; font-weight:500; color:var(--text-muted);">trx</span></div>
        <div class="stat-icon purple">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
    </div>
    <div class="stat-card amber">
        <div class="stat-label">Pendapatan Bulan Ini</div>
        <div class="stat-value">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</div>
        <div class="stat-icon amber">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
    </div>
    <div class="stat-card blue">
        <div class="stat-label">Produk Aktif</div>
        <div class="stat-value">
            <span>{{ $totalProducts }}</span>
            @if($lowStockProducts > 0)
                <span class="badge badge-warning" style="font-size:11px; margin-left:6px;">{{ $lowStockProducts }} menipis</span>
            @endif
        </div>
        <div class="stat-icon blue">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
    </div>
</div>

{{-- CHARTS + RECENT --}}
<div class="chart-grid">
    {{-- GRAFIK PENDAPATAN 7 HARI --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Pendapatan 7 Hari Terakhir
            </div>
            <span style="font-size:12px; color:var(--text-muted);">Tren mingguan</span>
        </div>
        <div class="card-body" style="padding: 20px;">
            <div style="height: 250px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- TRANSAKSI TERBARU --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Transaksi Terbaru
            </div>
            <a href="{{ route('transactions.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
        </div>
        <div style="overflow-y: auto; max-height: 280px; padding: 0 16px;">
            @forelse($recentTransactions as $trx)
            <div style="padding: 12px 6px; border-bottom: 1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <a href="{{ route('transactions.show', $trx) }}" style="font-size:13px; font-weight:700; color:var(--primary-light); text-decoration:none;">
                        {{ $trx->invoice_number }}
                    </a>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                        {{ $trx->created_at->format('H:i') }} · {{ $trx->customer?->name ?? 'Umum' }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:13px; font-weight:800; color:var(--success);">Rp {{ number_format($trx->total, 0, ',', '.') }}</div>
                    <span class="badge badge-{{ $trx->status === 'completed' ? 'success' : 'danger' }}" style="font-size:9px; margin-top:2px;">
                        {{ $trx->status === 'completed' ? 'Selesai' : 'Batal' }}
                    </span>
                </div>
            </div>
            @empty
            <div style="padding:40px; text-align:center; color:var(--text-muted);">
                <div style="font-size:28px; margin-bottom:6px; opacity:0.5;">🛒</div>
                <div>Belum ada transaksi hari ini</div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart');
const isLightInit = document.documentElement.getAttribute('data-theme') === 'light';
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: @json($chartData),
            backgroundColor: isLightInit ? 'rgba(5, 150, 105, 0.75)' : 'rgba(16, 185, 129, 0.7)',
            borderColor: isLightInit ? '#059669' : '#10b981',
            borderWidth: 1,
            borderRadius: 6,
            hoverBackgroundColor: isLightInit ? '#10b981' : '#34d399',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (ctx) => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    color: isLightInit ? '#64748b' : '#94a3b8',
                    font: { family: 'Inter' }
                }
            },
            y: {
                beginAtZero: true,
                grid: { color: isLightInit ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.05)' },
                ticks: {
                    color: isLightInit ? '#64748b' : '#94a3b8',
                    font: { family: 'Inter' },
                    callback: (val) => val >= 1000000 ? (val/1000000) + 'jt' : (val >= 1000 ? (val/1000) + 'rb' : val)
                }
            }
        }
    }
});

window.addEventListener('themechange', (e) => {
    const isLight = e.detail.theme === 'light';
    const chart = Chart.getChart('revenueChart');
    if (chart) {
        chart.data.datasets[0].backgroundColor = isLight ? 'rgba(5, 150, 105, 0.75)' : 'rgba(16, 185, 129, 0.7)';
        chart.data.datasets[0].borderColor = isLight ? '#059669' : '#10b981';
        chart.data.datasets[0].hoverBackgroundColor = isLight ? '#10b981' : '#34d399';
        chart.options.scales.x.ticks.color = isLight ? '#64748b' : '#94a3b8';
        chart.options.scales.y.ticks.color = isLight ? '#64748b' : '#94a3b8';
        chart.options.scales.y.grid.color = isLight ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.05)';
        chart.update();
    }
});
</script>
@endpush
