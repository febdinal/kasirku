@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Penjualan Bulanan')
@section('page-subtitle', 'Analisis tren penjualan dan performa transaksi per bulan')

@push('styles')
<style>
    .chart-wrap {
        position: relative;
        height: 280px;
        width: 100%;
    }
</style>
@endpush

@section('content')
{{-- FILTER BULAN --}}
<div class="filter-card">
    <form method="GET" class="filter-form">
        <div class="filter-group">
            <label class="filter-label">Pilih Bulan & Tahun</label>
            <input type="month" name="month" class="form-control" value="{{ $month }}" style="width:200px;">
        </div>
        <div class="filter-actions" style="margin-bottom:1px;">
            <button type="submit" class="btn btn-primary">Tampilkan Laporan</button>
            <a href="{{ route('reports.monthly') }}" class="btn btn-ghost">Bulan Ini</a>
        </div>
    </form>
</div>

{{-- STAT CARDS --}}
<div class="grid-4 mb-6">
    <div class="stat-card green">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="stat-icon green">💰</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">{{ number_format($totalTransactions) }} <span style="font-size:14px; font-weight:500; color:var(--text-muted);">transaksi</span></div>
        <div class="stat-icon purple">🛒</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-label">Produk Terjual</div>
        <div class="stat-value">{{ number_format($totalItemsSold) }} <span style="font-size:14px; font-weight:500; color:var(--text-muted);">pcs</span></div>
        <div class="stat-icon blue">📦</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-label">Rata-rata / Transaksi</div>
        <div class="stat-value">Rp {{ $totalTransactions ? number_format($totalRevenue / $totalTransactions, 0, ',', '.') : 0 }}</div>
        <div class="stat-icon amber">📊</div>
    </div>
</div>

<div class="grid-2">
    {{-- GRAFIK HARIAN --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>📈 Tren Pendapatan Harian</span>
            </div>
            <span style="font-size:12px; color:var(--text-muted);">{{ \Carbon\Carbon::createFromDate((int)$year, (int)$monthNum, 1)->translatedFormat('F Y') }}</span>
        </div>
        <div class="card-body">
            <div class="chart-wrap">
                <canvas id="monthChart"></canvas>
            </div>
        </div>
    </div>

    {{-- BREAKDOWN METODE BAYAR --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span>💳 Metode Pembayaran</span>
            </div>
            <span style="font-size:12px; color:var(--text-muted);">Pangsa transaksi</span>
        </div>
        <div class="card-body">
            @forelse($paymentBreakdown as $pm)
            @php $pct = $totalRevenue > 0 ? round(($pm->total / $totalRevenue) * 100, 1) : 0; @endphp
            <div style="margin-bottom:18px;">
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px; margin-bottom:6px;">
                    <div style="font-weight:700; text-transform:uppercase; display:flex; align-items:center; gap:8px;">
                        <span>{{ $pm->payment_method }}</span>
                        <span class="badge badge-purple" style="font-size:10px;">{{ $pm->count }} trx</span>
                    </div>
                    <div style="font-weight:800; color:var(--success);">
                        Rp {{ number_format($pm->total, 0, ',', '.') }} <span style="font-size:11px; color:var(--text-muted); font-weight:500;">({{ $pct }}%)</span>
                    </div>
                </div>
                <div style="background:var(--bg-elevated); border-radius:6px; height:8px; overflow:hidden;">
                    <div style="height:100%; border-radius:6px; background:linear-gradient(90deg, var(--primary), var(--primary-light)); width:{{ $pct }}%;"></div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:50px 20px; color:var(--text-muted);">
                <div style="font-size:32px; margin-bottom:8px; opacity:0.6;">💳</div>
                <div>Belum ada data transaksi di bulan ini</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- RINCIAN PER HARI --}}
<div class="card mt-6">
    <div class="card-header">
        <div class="card-title">Rincian Penjualan Per Hari</div>
        <span style="font-size:12px; color:var(--text-muted);">{{ count($dailyData) }} hari aktif</span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Tanggal</th>
                    <th style="text-align:center;">Jumlah Transaksi</th>
                    <th class="text-right">Total Pendapatan</th>
                    <th style="text-align:right; width:130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dailyData as $index => $d)
                <tr>
                    <td class="text-muted">{{ $index + 1 }}</td>
                    <td style="font-weight:600; color:var(--text-primary);">
                        {{ \Carbon\Carbon::parse($d->date)->translatedFormat('l, d F Y') }}
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-purple">{{ $d->transaction_count }} transaksi</span>
                    </td>
                    <td class="text-right" style="font-weight:800; color:var(--success); font-size:14px;">
                        Rp {{ number_format($d->revenue, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('reports.daily', ['date' => $d->date]) }}" class="btn btn-ghost btn-sm">
                            Detail &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted);">Tidak ada data penjualan di bulan ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('monthChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    // Gradient background
    const isLightMonthly = document.documentElement.getAttribute('data-theme') === 'light';
    const gradient = ctx.createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, isLightMonthly ? 'rgba(5, 150, 105, 0.35)' : 'rgba(16, 185, 129, 0.35)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0.0)');

    const labels = @json($chartLabels);
    const revenues = @json($chartRevenue);
    const transactions = @json($chartTransactions);
    const monthName = '{{ \Carbon\Carbon::createFromDate((int)$year, (int)$monthNum, 1)->translatedFormat('F Y') }}';

    const maxVal = Math.max(...revenues);
    const suggestedMax = maxVal > 0 ? Math.ceil(maxVal * 1.25 / 10000) * 10000 : 50000;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan',
                data: revenues,
                borderColor: isLightMonthly ? '#059669' : '#10b981',
                borderWidth: 2.5,
                backgroundColor: gradient,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: isLightMonthly ? '#059669' : '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: function(context) {
                    return context.raw > 0 ? 5 : 0;
                },
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f1f5f9',
                    bodyColor: isLightMonthly ? '#10b981' : '#34d399',
                    borderColor: 'rgba(255,255,255,0.12)',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        title: function(items) {
                            return 'Tanggal ' + items[0].label + ' ' + monthName;
                        },
                        label: function(ctx) {
                            const idx = ctx.dataIndex;
                            const rev = ctx.raw;
                            const trx = transactions[idx] || 0;
                            return [
                                'Pendapatan: Rp ' + rev.toLocaleString('id-ID'),
                                'Transaksi: ' + trx + ' transaksi'
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: document.documentElement.getAttribute('data-theme') === 'light' ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: document.documentElement.getAttribute('data-theme') === 'light' ? '#64748b' : '#94a3b8',
                        font: { family: 'Inter', size: 11 },
                        autoSkip: true,
                        maxTicksLimit: 16
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: suggestedMax,
                    grid: { color: document.documentElement.getAttribute('data-theme') === 'light' ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: document.documentElement.getAttribute('data-theme') === 'light' ? '#64748b' : '#94a3b8',
                        font: { family: 'Inter', size: 11 },
                        callback: function(val) {
                            if (val === 0) return 'Rp 0';
                            if (val >= 1000000) return 'Rp ' + (val / 1000000).toLocaleString('id-ID') + ' jt';
                            if (val >= 1000) return 'Rp ' + (val / 1000).toLocaleString('id-ID') + ' rb';
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    window.addEventListener('themechange', (e) => {
        const isLight = e.detail.theme === 'light';
        const chart = Chart.getChart('monthChart');
        if (chart) {
            chart.data.datasets[0].borderColor = isLight ? '#059669' : '#10b981';
            chart.data.datasets[0].pointBackgroundColor = isLight ? '#059669' : '#10b981';
            chart.options.scales.x.ticks.color = isLight ? '#64748b' : '#94a3b8';
            chart.options.scales.y.ticks.color = isLight ? '#64748b' : '#94a3b8';
            chart.options.scales.x.grid.color = isLight ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.04)';
            chart.options.scales.y.grid.color = isLight ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.04)';
            chart.update();
        }
    });
});
</script>
@endpush
