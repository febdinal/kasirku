@extends('layouts.app')
@section('title', 'Produk')
@section('page-title', 'Manajemen Produk')
@section('page-subtitle', 'Katalog barang dan inventaris stok')

@section('topbar-actions')
    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Produk
    </a>
@endsection

@push('styles')
<style>
    .product-thumb-box {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-sm);
        overflow: hidden;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
        position: relative;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .product-thumb-box:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
        transform: scale(1.08);
    }
    .product-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-thumb-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        opacity: 0.5;
        background: rgba(255, 255, 255, 0.02);
    }
    .img-preview-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 15, 30, 0.85);
        backdrop-filter: blur(8px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }
    .img-preview-modal.active {
        display: flex;
    }
    .img-preview-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 20px;
        max-width: 440px;
        width: 100%;
        text-align: center;
        position: relative;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
    }
    .img-preview-close {
        position: absolute;
        top: 12px;
        right: 14px;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        color: var(--text-muted);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        line-height: 1;
        transition: all 0.2s ease;
    }
    .img-preview-close:hover {
        color: #fff;
        border-color: var(--danger);
        background: rgba(239, 68, 68, 0.2);
    }
    #img-preview-target {
        width: 100%;
        max-height: 320px;
        object-fit: contain;
        border-radius: var(--radius-sm);
        background: var(--bg-elevated);
        margin: 8px 0 12px;
    }
    .img-preview-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
    }
</style>
@endpush

@section('content')
{{-- FILTER BAR --}}
<div class="filter-card">
    <form method="GET" class="filter-form">
        <div class="filter-group" style="flex:2; min-width:220px;">
            <label class="filter-label">Pencarian</label>
            <input type="text" name="search" class="form-control" placeholder="Cari nama produk..." value="{{ request('search') }}">
        </div>
        <div class="filter-group" style="min-width:180px;">
            <label class="filter-label">Kategori</label>
            <select name="category_id" class="form-control">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions" style="margin-bottom:1px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->hasAny(['search','category_id']))
                <a href="{{ route('products.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span>Daftar Produk</span>
            <span class="badge badge-purple" style="margin-left:8px;">{{ $products->total() }} produk</span>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
            + Tambah Produk
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:40px; text-align:center;">#</th>
                    <th style="width:65px; text-align:center;">Foto</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="text-right">Harga Jual</th>
                    <th style="text-align:center;">Stok</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right; width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                <tr>
                    <td class="text-muted" style="text-align:center;">{{ $products->firstItem() + $index }}</td>
                    <td style="text-align:center;">
                        <div class="product-thumb-box" @if($product->image) onclick="openImagePreview('{{ asset('storage/' . $product->image) }}', '{{ addslashes($product->name) }}')" title="Klik untuk memperbesar" @endif>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-thumb-img" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\'product-thumb-placeholder\'><svg width=\'18\' height=\'18\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>'">
                            @else
                                <div class="product-thumb-placeholder" title="Tidak ada foto">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:700; color:var(--text-primary);">{{ $product->name }}</div>
                        @if($product->description)
                            <div style="font-size:11px; color:var(--text-muted); max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $product->description }}</div>
                        @endif
                    </td>
                    <td>
                        @if($product->category)
                            <span class="badge badge-purple">{{ $product->category->name }}</span>
                        @else
                            <span class="text-muted">Umum</span>
                        @endif
                    </td>
                    <td class="text-right" style="font-weight:700; color:var(--success);">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td style="text-align:center;">
                        @if($product->stock <= 0)
                            <span class="badge badge-danger">Habis</span>
                        @elseif($product->stock <= 5)
                            <span class="badge badge-warning">{{ $product->stock }} {{ $product->unit }}</span>
                        @else
                            <span style="font-weight:600; color:var(--text-primary);">{{ $product->stock }} {{ $product->unit }}</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-{{ $product->is_active ? 'success' : 'danger' }}">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <div class="flex gap-2" style="justify-content:flex-end;">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:50px; color:var(--text-muted);">
                        <div style="font-size:32px; margin-bottom:8px; opacity:0.6;">📦</div>
                        <div style="font-weight:600;">Tidak ada produk yang ditemukan</div>
                        <div style="font-size:12px; margin-top:4px;">Coba ubah kata kunci pencarian atau tambahkan produk baru</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="pagination-wrapper">
        <div style="font-size:12px; color:var(--text-muted);">
            Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} produk
        </div>
        <div>{{ $products->links('pagination::simple-bootstrap-5') }}</div>
    </div>
    @endif
</div>

{{-- MODAL PREVIEW GAMBAR BESAR --}}
<div id="image-preview-modal" class="img-preview-modal" onclick="closeImagePreview()">
    <div class="img-preview-card" onclick="event.stopPropagation()">
        <button type="button" class="img-preview-close" onclick="closeImagePreview()" aria-label="Tutup">&times;</button>
        <img id="img-preview-target" src="" alt="Preview Produk">
        <div id="img-preview-title" class="img-preview-title"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openImagePreview(src, title) {
    document.getElementById('img-preview-target').src = src;
    document.getElementById('img-preview-title').textContent = title;
    document.getElementById('image-preview-modal').classList.add('active');
}
function closeImagePreview() {
    document.getElementById('image-preview-modal').classList.remove('active');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeImagePreview();
});
</script>
@endpush
