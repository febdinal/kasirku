@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk Baru')
@section('page-subtitle', 'Lengkapi data produk untuk inventaris dan POS')

@section('topbar-actions')
    <a href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Produk
    </a>
@endsection

@section('content')
<div style="max-width:820px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Informasi Produk
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Produk *</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Kopi Susu Gula Aren" value="{{ old('name') }}" required autofocus>
                        @error('name')<div style="color:#fca5a5; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">SKU / Kode Barcode</label>
                        <input type="text" name="sku" class="form-control font-mono" value="{{ old('sku') }}" placeholder="Auto jika dikosongkan">
                        @error('sku')<div style="color:#fca5a5; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Satuan Unit *</label>
                        <input type="text" name="unit" class="form-control" value="{{ old('unit', 'pcs') }}" placeholder="pcs, botol, porsi, kg" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Jual (Rp) *</label>
                        <input type="number" name="price" class="form-control font-mono" value="{{ old('price', 0) }}" min="0" step="100" required>
                        @error('price')<div style="color:#fca5a5; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Modal / HPP (Rp)</label>
                        <input type="number" name="cost_price" class="form-control font-mono" value="{{ old('cost_price', 0) }}" min="0" step="100">
                        <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Digunakan untuk menghitung laporan laba rugi</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok Awal *</label>
                        <input type="number" name="stock" class="form-control font-mono" value="{{ old('stock', 0) }}" min="0" required>
                    </div>
                    <div class="form-group" style="display:flex; flex-direction:column; justify-content:center;">
                        <label class="form-label">Status Produk</label>
                        <label class="toggle-switch" style="margin-top:4px;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                            <span style="font-size:13px; font-weight:600; color:var(--text-primary);">Produk Aktif untuk Dijual</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Produk (Opsional)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan keterangan detail atau varian produk...">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto / Gambar Produk (Opsional)</label>
                    <div id="image-preview-container" style="display:none; margin-bottom:10px; align-items:center; gap:12px; background:var(--bg-elevated); padding:8px 12px; border-radius:var(--radius-sm); border:1px solid var(--border);">
                        <img id="image-preview-img" src="" alt="Preview" style="height:54px; width:54px; object-fit:cover; border-radius:6px;">
                        <span style="font-size:12px; color:var(--text-muted);">Pratinjau gambar baru</span>
                    </div>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewSelectedImage(this)">
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px; padding-top:16px; border-top:1px solid var(--border-subtle);">
                    <a href="{{ route('products.index') }}" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewSelectedImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview-img').src = e.target.result;
            document.getElementById('image-preview-container').style.display = 'flex';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
