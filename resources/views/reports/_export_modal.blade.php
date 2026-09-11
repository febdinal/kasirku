{{-- MODAL EXPORT EXCEL --}}
<div class="modal-overlay" id="export-excel-modal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title" style="display:flex; align-items:center; gap:8px;">
                <span>📊</span> Export Laporan Penjualan ke Excel
            </div>
            <button type="button" class="modal-close-btn" onclick="closeExportModal()">&times;</button>
        </div>

        <form action="{{ route('reports.export-excel') }}" method="GET" target="_blank">
            <div style="margin-bottom:18px;">
                <label class="form-label" style="margin-bottom:8px; font-weight:700;">Pilih Periode Laporan:</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <label class="radio-card" style="display:flex; align-items:center; gap:8px; padding:10px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer; background:var(--bg-elevated);">
                        <input type="radio" name="period_type" value="daily" checked onchange="switchExportPeriod(this.value)">
                        <span style="font-size:13px; font-weight:600;">📅 Harian</span>
                    </label>
                    <label class="radio-card" style="display:flex; align-items:center; gap:8px; padding:10px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer; background:var(--bg-elevated);">
                        <input type="radio" name="period_type" value="weekly" onchange="switchExportPeriod(this.value)">
                        <span style="font-size:13px; font-weight:600;">🗓️ Mingguan (7 Hari)</span>
                    </label>
                    <label class="radio-card" style="display:flex; align-items:center; gap:8px; padding:10px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer; background:var(--bg-elevated);">
                        <input type="radio" name="period_type" value="monthly" onchange="switchExportPeriod(this.value)">
                        <span style="font-size:13px; font-weight:600;">📆 Bulanan</span>
                    </label>
                    <label class="radio-card" style="display:flex; align-items:center; gap:8px; padding:10px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer; background:var(--bg-elevated);">
                        <input type="radio" name="period_type" value="custom" onchange="switchExportPeriod(this.value)">
                        <span style="font-size:13px; font-weight:600;">🎯 Rentang Custom</span>
                    </label>
                </div>
            </div>

            {{-- FIELD HARIAN --}}
            <div id="export-field-daily" class="export-period-field">
                <div class="form-group">
                    <label class="form-label">Pilih Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ today()->toDateString() }}">
                </div>
            </div>

            {{-- FIELD MINGGUAN --}}
            <div id="export-field-weekly" class="export-period-field" style="display:none;">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="export-weekly-start" class="form-control" value="{{ today()->subDays(6)->toDateString() }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Sampai</label>
                        <input type="date" name="end_date" id="export-weekly-end" class="form-control" value="{{ today()->toDateString() }}">
                    </div>
                </div>
                <div style="font-size:11px; color:var(--text-muted); margin-top:-8px; margin-bottom:12px;">Default: 7 hari terakhir</div>
            </div>

            {{-- FIELD BULANAN --}}
            <div id="export-field-monthly" class="export-period-field" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Pilih Bulan & Tahun</label>
                    <input type="month" name="month" class="form-control" value="{{ today()->format('Y-m') }}">
                </div>
            </div>

            {{-- FIELD CUSTOM RANGE --}}
            <div id="export-field-custom" class="export-period-field" style="display:none;">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="custom_start_date" id="export-custom-start" class="form-control" value="{{ today()->startOfMonth()->toDateString() }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="custom_end_date" id="export-custom-end" class="form-control" value="{{ today()->toDateString() }}">
                    </div>
                </div>
                <div style="font-size:11px; color:var(--text-muted); margin-top:-8px; margin-bottom:12px;">Pilih tanggal awal dan tanggal akhir secara bebas</div>
            </div>

            <div style="padding:12px; background:var(--bg-elevated); border-radius:var(--radius-sm); border:1px solid var(--border); font-size:12px; color:var(--text-secondary); margin-bottom:20px;">
                💡 File yang diunduh berformat <strong>Excel (.xls)</strong> lengkap dengan ringkasan omset, rincian faktur transaksi, metode bayar, dan rekap produk.
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeExportModal()" class="btn btn-ghost">Tutup</button>
                <button type="submit" class="btn btn-success" style="padding:10px 20px;">
                    📥 Unduh File Excel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openExportModal(defaultType = 'daily', defaultVal = '') {
    if (defaultType) {
        const radio = document.querySelector(`input[name="period_type"][value="${defaultType}"]`);
        if (radio) {
            radio.checked = true;
            switchExportPeriod(defaultType);
        }
        if (defaultType === 'daily' && defaultVal) {
            const dateInput = document.querySelector('#export-field-daily input[name="date"]');
            if (dateInput) dateInput.value = defaultVal;
        } else if (defaultType === 'monthly' && defaultVal) {
            const monthInput = document.querySelector('#export-field-monthly input[name="month"]');
            if (monthInput) monthInput.value = defaultVal;
        }
    }
    document.getElementById('export-excel-modal').classList.add('active');
}

function closeExportModal() {
    document.getElementById('export-excel-modal').classList.remove('active');
}

function switchExportPeriod(val) {
    document.querySelectorAll('.export-period-field').forEach(el => el.style.display = 'none');
    
    // Sync name attributes between weekly and custom
    const weeklyStart = document.getElementById('export-weekly-start');
    const weeklyEnd = document.getElementById('export-weekly-end');
    const customStart = document.getElementById('export-custom-start');
    const customEnd = document.getElementById('export-custom-end');

    if (val === 'daily') {
        document.getElementById('export-field-daily').style.display = 'block';
    } else if (val === 'weekly') {
        document.getElementById('export-field-weekly').style.display = 'block';
        weeklyStart.name = 'start_date';
        weeklyEnd.name = 'end_date';
        customStart.name = 'unused_start';
        customEnd.name = 'unused_end';
    } else if (val === 'monthly') {
        document.getElementById('export-field-monthly').style.display = 'block';
    } else if (val === 'custom') {
        document.getElementById('export-field-custom').style.display = 'block';
        customStart.name = 'start_date';
        customEnd.name = 'end_date';
        weeklyStart.name = 'unused_start';
        weeklyEnd.name = 'unused_end';
    }
}

document.getElementById('export-excel-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeExportModal();
});
</script>
