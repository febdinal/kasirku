@extends('layouts.app')
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')
@section('page-subtitle', $product->name)

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
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Perbarui Data: {{ $product->name }}
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Nama Produk *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required autofocus>
                        @error('name')<div style="color:#fca5a5; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">SKU / Kode Barcode</label>
                        <input type="text" name="sku" class="form-control font-mono" value="{{ old('sku', $product->sku) }}" placeholder="Kode barcode">
                        @error('sku')<div style="color:#fca5a5; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Satuan Unit *</label>
                        <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Jual (Rp) *</label>
                        <input type="number" name="price" class="form-control font-mono" value="{{ old('price', $product->price) }}" min="0" step="100" required>
                        @error('price')<div style="color:#fca5a5; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Modal / HPP (Rp)</label>
                        <input type="number" name="cost_price" class="form-control font-mono" value="{{ old('cost_price', $product->cost_price) }}" min="0" step="100">
                        <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Digunakan untuk menghitung laporan laba rugi</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok *</label>
                        <input type="number" name="stock" class="form-control font-mono" value="{{ old('stock', $product->stock) }}" min="0" required>
                    </div>
                    <div class="form-group" style="display:flex; flex-direction:column; justify-content:center;">
                        <label class="form-label">Status Produk</label>
                        <label class="toggle-switch" style="margin-top:4px;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                            <span style="font-size:13px; font-weight:600; color:var(--text-primary);">Produk Aktif untuk Dijual</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Produk (Opsional)</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Produk (Biarkan kosong jika tidak diubah)</label>
                    @if($product->image)
                        <div id="current-image-container" style="margin-bottom:10px; display:flex; align-items:center; gap:12px; background:var(--bg-elevated); padding:8px 12px; border-radius:var(--radius-sm); border:1px solid var(--border);">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="Preview" style="height:50px; width:50px; object-fit:cover; border-radius:6px;">
                            <span style="font-size:12px; color:var(--text-muted);">Foto saat ini tersimpan</span>
                        </div>
                    @endif
                    <div id="image-preview-container" style="display:none; margin-bottom:10px; align-items:center; gap:12px; background:var(--bg-elevated); padding:8px 12px; border-radius:var(--radius-sm); border:1px solid var(--border);">
                        <img id="image-preview-img" src="" alt="Preview Baru" style="height:50px; width:50px; object-fit:cover; border-radius:6px;">
                        <span style="font-size:12px; color:var(--success);">Pratinjau foto baru yang dipilih</span>
                    </div>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewSelectedImage(this)">
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px; padding-top:16px; border-top:1px solid var(--border-subtle);">
                    <a href="{{ route('products.index') }}" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
