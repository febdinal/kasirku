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

        {{-- TOMBOL SIMPAN --}}
        <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:40px;">
            <button type="submit" class="btn btn-primary btn-lg" style="padding:12px 28px;">
                💾 Simpan Semua Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
