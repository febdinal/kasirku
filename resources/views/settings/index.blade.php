@extends('layouts.app')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Toko')
@section('page-subtitle', 'Konfigurasi identitas toko, ketentuan pajak, dan nota struk')

@section('content')
<div style="max-width:820px; margin: 0 auto;">
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf @method('PUT')

        {{-- SEKSI 1: IDENTITAS TOKO --}}
        <div class="card mb-6">
            <div class="card-header">
                <div class="card-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Identitas & Informasi Toko
                </div>
            </div>
            <div class="card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Toko *</label>
                        <input type="text" name="store_name" class="form-control" value="{{ $settings['store_name']?->value ?? 'VENTRA' }}" placeholder="Contoh: VENTRA" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Slogan / Tagline</label>
                        <input type="text" name="store_tagline" class="form-control" value="{{ $settings['store_tagline']?->value ?? '' }}" placeholder="Contoh: Belanja Hemat, Pasti Lengkap">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat Lengkap Toko</label>
                    <textarea name="store_address" class="form-control" rows="2" placeholder="Jl. Raya No. 123, Kota...">{{ $settings['store_address']?->value ?? '' }}</textarea>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="store_phone" class="form-control" value="{{ $settings['store_phone']?->value ?? '' }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="store_email" class="form-control" value="{{ $settings['store_email']?->value ?? '' }}" placeholder="toko@domain.com">
                    </div>
                </div>
            </div>
        </div>

        {{-- SEKSI 2: KEUANGAN & PAJAK --}}
        <div class="card mb-6">
            <div class="card-header">
                <div class="card-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Ketentuan Finansial & Pajak (PPN)
                </div>
            </div>
            <div class="card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Mata Uang Simbol</label>
                        <input type="text" name="currency_symbol" class="form-control font-mono" value="{{ $settings['currency_symbol']?->value ?? 'Rp' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Persentase Tarif PPN (%)</label>
                        <input type="number" name="ppn_percentage" class="form-control font-mono" value="{{ $settings['ppn_percentage']?->value ?? 11 }}" min="0" max="100" step="0.1" required>
                    </div>
                </div>

                <div class="form-group" style="padding:16px; background:var(--bg-elevated); border-radius:var(--radius-md); border:1px solid var(--border); margin-bottom:0;">
                    <input type="hidden" name="ppn_enabled" value="0">
                    <label class="toggle-switch">
                        <input type="checkbox" name="ppn_enabled" id="ppn_enabled" value="1"
                            {{ ($settings['ppn_enabled']?->value ?? '0') === '1' ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                        <div>
                            <div style="font-size:13px; font-weight:700; color:var(--text-primary);">Aktifkan PPN Otomatis pada Kasir POS</div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Jika aktif, nilai PPN akan dihitung dan ditampilkan di struk belanja</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- SEKSI 3: NOTA STRUK --}}
        <div class="card mb-6">
            <div class="card-header">
                <div class="card-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Format Struk Pembayaran
                </div>
            </div>
            <div class="card-body">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Pesan Catatan Footer Struk</label>
                    <textarea name="receipt_footer" class="form-control" rows="3" placeholder="Terima kasih atas kunjungan Anda...">{{ $settings['receipt_footer']?->value ?? '' }}</textarea>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Teks ini akan dicetak di bagian paling bawah setiap struk transaksi</div>
                </div>
            </div>
        </div>

        {{-- TOMBOL SIMPAN PENGATURAN UMUM --}}
        <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:28px;">
            <button type="submit" class="btn btn-primary btn-lg" style="padding:12px 28px;">
                💾 Simpan Konfigurasi Toko & Pajak
            </button>
        </div>
    </form>

    {{-- SEKSI 4: MANAJEMEN METODE PEMBAYARAN --}}
    <div class="card mb-6">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Daftar Metode Pembayaran</span>
                <span class="badge badge-purple" style="margin-left:8px;">{{ $paymentMethods->count() }} Metode</span>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openPaymentModal()">
                + Tambah Metode
            </button>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-container" style="border:none;">
                <table>
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Nama Metode</th>
                            <th>Kode Sistem</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th style="text-align:right; width:180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentMethods as $index => $pm)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div style="font-weight:700; color:var(--text-primary);">{{ $pm->name }}</div>
                            </td>
                            <td>
                                <code style="font-size:12px; background:var(--bg-elevated); padding:3px 8px; border-radius:4px; border:1px solid var(--border);">{{ $pm->code }}</code>
                            </td>
                            <td>
                                @if($pm->is_cash)
                                    <span class="badge" style="background:rgba(16, 185, 129, 0.15); color:var(--success); border:1px solid rgba(16, 185, 129, 0.3);">💵 Tunai</span>
                                @else
                                    <span class="badge" style="background:rgba(99, 102, 241, 0.15); color:#818cf8; border:1px solid rgba(99, 102, 241, 0.3);">💳 Non-Tunai</span>
                                @endif
                            </td>
                            <td>
                                @if($pm->is_active)
                                    <span class="badge badge-success">✓ Aktif</span>
                                @else
                                    <span class="badge" style="background:rgba(239, 68, 68, 0.12); color:#f87171; border:1px solid rgba(239, 68, 68, 0.25);">✕ Nonaktif</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; justify-content:flex-end; gap:6px;">
                                    <form action="{{ route('settings.payment-methods.toggle', $pm) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-ghost btn-sm" title="{{ $pm->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            {{ $pm->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    @if($pm->code !== 'cash')
                                    <form action="{{ route('settings.payment-methods.destroy', $pm) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus metode pembayaran ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">
                                Belum ada data metode pembayaran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH METODE PEMBAYARAN --}}
<div class="modal-overlay" id="payment-method-modal">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <div class="modal-title">+ Tambah Metode Pembayaran Baru</div>
            <button type="button" class="modal-close-btn" onclick="closePaymentModal()">&times;</button>
        </div>
        <form action="{{ route('settings.payment-methods.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Metode Pembayaran *</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: ShopeePay, OVO, Transfer BCA" required autofocus>
                <div style="font-size:11px; color:var(--text-muted); margin-top:3px;">Nama ini akan muncul pada opsi pembayaran di kasir POS.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Kode Singkat (Opsional)</label>
                <input type="text" name="code" class="form-control font-mono" placeholder="Contoh: shopeepay (otomatis jika kosong)">
            </div>

            <div class="form-group" style="padding:14px; background:var(--bg-elevated); border-radius:var(--radius-md); border:1px solid var(--border);">
                <label class="toggle-switch">
                    <input type="checkbox" name="is_cash" value="1">
                    <span class="toggle-slider"></span>
                    <div>
                        <div style="font-size:13px; font-weight:700; color:var(--text-primary);">Metode Pembayaran Tunai (Cash)</div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Jika dicentang, kasir akan meminta input nominal uang yang diterima dan menghitung kembalian.</div>
                    </div>
                </label>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closePaymentModal()" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Metode</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPaymentModal() {
    document.getElementById('payment-method-modal').classList.add('active');
}
function closePaymentModal() {
    document.getElementById('payment-method-modal').classList.remove('active');
}
document.getElementById('payment-method-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closePaymentModal();
});
</script>
@endpush
